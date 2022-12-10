<?php

class AnalisadorAritimeticoCYK {

    public static function aceita(string $input) : bool
    {
        $input = str_replace(' ', '', $input);

        $simbolos = str_split($input);

        $tabelaT = [];

        $tabelaT[] = self::getPrimeiraLinhaTabela($simbolos);

        for ($indiceLinha=1; $indiceLinha < count($simbolos); $indiceLinha++) { 
            $tabelaT[] = self::getLinhaTabela($tabelaT, $indiceLinha);
        }

        return in_array(self::getSimboloInicial(), $tabelaT[count($simbolos) - 1][0]);
    }

    private static function getPrimeiraLinhaTabela(array $simbolos) : array 
    {
        $linha = [];

        foreach ($simbolos as $simbolo) {
            $linha[] = [self::getVariavelProduzTerminal($simbolo)];
        }
        
        return $linha;
    }

    private static function getLinhaTabela(array $tabela, int $indiceLinha) : array
    {
        $colunasUltimaLinha = $tabela[$indiceLinha - 1];

        $linha = [];

        for ($colunaTabela=0; $colunaTabela < count($colunasUltimaLinha) - 1; $colunaTabela++) { 
            $linha[] = self::getVariaveisCelula($indiceLinha, $colunaTabela, $tabela);
        }

        return $linha;
    }

    private static function getVariaveisCelula(int $indiceLinha, int $indiceColuna, array $tabela) : array
    {
        $variaveis = [];

        $indiceLinhaSegundaCelula = 0;
        $indiceColunaSegundaCelula = $indiceLinha;

        for ($indiceLinhaPrimeiraCelula = ($indiceLinha - 1); $indiceLinhaPrimeiraCelula >= 0; $indiceLinhaPrimeiraCelula--) { 
            $primeiraCelula = $tabela[$indiceLinhaPrimeiraCelula][$indiceColuna];
            $segundaCelula = $tabela[$indiceLinhaSegundaCelula][$indiceColunaSegundaCelula];

            foreach ($primeiraCelula as $variavelPrimeiraCelula) {
                foreach ($segundaCelula as $variavelSegundaCelula) {
                    $variaveis = array_merge($variaveis, self::getVariaveisGerarCombinacao($variavelPrimeiraCelula, $variavelSegundaCelula));
                }
            }

            $indiceLinhaSegundaCelula++;
            $indiceColunaSegundaCelula--;
        }

        return array_unique($variaveis);
    }

    private static function getVariaveisGerarCombinacao(string $primeiraVariavel, string $segundaVariavel) : array
    {
        $variaveis = [];

        foreach (self::getRegras() as $variavel => $producoes) {
            foreach ($producoes as $simbolos) {
                if(count($simbolos) === 2 && $simbolos[0] === $primeiraVariavel && $simbolos[01] === $segundaVariavel) {
                    $variaveis[] = $variavel;
                }
            }
        }

        return $variaveis;
    }

    private static function getVariavelProduzTerminal(string $terminal) : string 
    {
        foreach (self::getRegras() as $variavel => $producoes) {
            foreach ($producoes as $simbolos) {
                if(count($simbolos) === 1 && $simbolos[0] === $terminal) {
                    return $variavel;
                }
            }
        }
    }

    /**
     * ID-UNITARIO -> 0 | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9 | 10
     * ID-COMPOSTO                -> ID-UNITARIO ID-UNITARIO | ID-UNITARIO ID-COMPOSTO
     * OPERADOR                   -> + | - | / | *
     * ABRE-PARENTESES            -> (
     * FECHA-PARENTESES           -> )
     * INICIO-OPERANDO-PARENTESES -> ABRE-PARENTESES ID-UNITARIO | ABRE-PARENTESES ID-COMPOSTO
     * OPERANDO-PARENTESES        -> INICIO-OPERANDO-PARENTESES FECHA-PARENTESES
     * INICIO-EXPRECAO            -> ID-UNITARIO OPERADOR | ID-COMPOSTO OPERADOR | OPERANDO-PARENTESES OPERADOR
     * EXPRECAO                   -> INICIO-EXPRECAO OPERANDO-PARENTESES 
     * EXPRECAO                   -> INICIO-EXPRECAO ID-UNITARIO
     * EXPRECAO                   -> INICIO-EXPRECAO ID-COMPOSTO
     * EXPRECAO                   -> INICIO-EXPRECAO EXPRECAO
     */
    
    private static function getRegras() : array
    {
        return [
            'ID-UNITARIO'                => [['0'], ['1'], ['2'], ['3'], ['4'], ['5'], ['6'], ['7'], ['8'], ['9']],
            'ID-COMPOSTO'                => [['ID-UNITARIO', 'ID-UNITARIO'], ['ID-UNITARIO', 'ID-COMPOSTO']],
            'OPERADOR'                   => [['+'], ['-'], ['*'], ['/']],
            'ABRE-PARENTESES'            => [['(']],
            'FECHA-PARENTESES'           => [[')']],
            'INICIO-OPERANDO-PARENTESES' => [['ABRE-PARENTESES', 'ID-UNITARIO'], ['ABRE-PARENTESES', 'ID-COMPOSTO']],
            'OPERANDO-PARENTESES'        => [['INICIO-OPERANDO-PARENTESES', 'FECHA-PARENTESES']],
            'INICIO-EXPRECAO'            => [
                ['ID-UNITARIO',         'OPERADOR'], 
                ['ID-COMPOSTO',         'OPERADOR'],
                ['OPERANDO-PARENTESES', 'OPERADOR']
            ],
            'EXPRECAO' => [
                ['INICIO-EXPRECAO', 'OPERANDO-PARENTESES'],
                ['INICIO-EXPRECAO', 'ID-UNITARIO'],
                ['INICIO-EXPRECAO', 'ID-COMPOSTO'],
                ['INICIO-EXPRECAO', 'EXPRECAO'],
            ]
        ];
    }

    private static function getSimboloInicial() : string 
    {
        return 'EXPRECAO';
    }
}