<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/*
Classe Select com uma lista de Bimestre do ano
* Data de Criação: 02/08/2006

* @author Desenvolvedor: Rodrigo

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

class Bimestre extends Select
{
    /**
     * Método Construtor
     * @access Public
    */

    function Bimestre()
    {
        parent::Select();
        // Monta array de bimestres e cria laço que adiciona ao select
        $arBimestre = array(
            1 => "1o Bimestre",
            2 => "2o Bimestre",
            3 => "3o Bimestre",
            4 => "4o Bimestre",
            5 => "5o Bimestre",
            6 => "6o Bimestre"
        );
        
        $this->setName  ( "bimestre" );
        $this->setRotulo( "Bimestre" );
        $this->setNull  (  false     );
        $this->addOption("","Selecione");
        
        for ($inContandorOpt=1;$inContandorOpt <= 6;$inContandorOpt++) {
            $this->addOption($inContandorOpt,$arBimestre[$inContandorOpt]);
        }
    }

    /**
    * Retorna a data inicial de um determinado bimestre
    *
    * @param    integer   $inBimestre    Valor do Bimestre
    * @param    string    $stExercicio   Valor do Exercício
    * @return   string    Data inicial do Bimestre
    */
    public static function getDataInicial($inBimestre, $stExercicio) {
        # Caso não tenha sido setado exercício, pega o exercício logado.
        if (empty($stExercicio)) {
            $stExercicio = Sessao::getExercicio();
        }

        if (!empty($inBimestre) && $inBimestre > 0) {
            switch ($inBimestre) {
                case 1: return "01/01/".$stExercicio; break;
                case 2: return '01/03/'.$stExercicio; break;
                case 3: return '01/05/'.$stExercicio; break;
                case 4: return '01/07/'.$stExercicio; break;
                case 5: return '01/09/'.$stExercicio; break;
                case 6: return '01/11/'.$stExercicio; break;
            }
        }
    }

    /**
    * Retorna a data final de um determinado bimestre
    *
    * @param    integer   $inBimestre    Valor do Bimestre
    * @param    string    $stExercicio   Valor do Exercício
    * @return   string    Data Final do Bimestre
    */
    public static function getDataFinal($inBimestre, $stExercicio) {
        # Caso não tenha sido setado exercício, pega o exercício logado.
        if (empty($stExercicio)) {
            $stExercicio = Sessao::getExercicio();
        }

        if (!empty($inBimestre) && $inBimestre > 0) {
            # Calculo para retornar o nro. de dias do mês de fevereiro.
            if ($inBimestre == 1) {
                $inNumDiasFevereiro = date('t', strtotime('02/01/'.$stExercicio));
            }

            switch ($inBimestre) {
                case 1: return $inNumDiasFevereiro.'/02/'.$stExercicio; break;
                case 2: return '30/04/'.$stExercicio; break;
                case 3: return '30/06/'.$stExercicio; break;
                case 4: return '31/08/'.$stExercicio; break;
                case 5: return '31/10/'.$stExercicio; break;
                case 6: return '31/12/'.$stExercicio; break;
            }
        }
    }
}


?>
