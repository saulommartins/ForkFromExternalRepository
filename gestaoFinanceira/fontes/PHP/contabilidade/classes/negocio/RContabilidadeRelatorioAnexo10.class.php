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
/**
    * Regra de negocio para anexo 10
    * Data de CriaÃ§Ã£o   : 06/10/2004

    * @author Analista: Jorge Ribarr

    * @author Desenvolvedor: Anderson Buzo
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    * $Id: RContabilidadeRelatorioAnexo10.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO                       );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"                              );

class RContabilidadeRelatorioAnexo10 extends PersistenteRelatorio
{

    /**
        * @var Object
        * @access Private
    */
    public $obFOrcamentoSomatorioReceitaOrcadaArrecadada;
    public $stFiltro;
    public $inExercicio;
    public $stDataInicial;
    public $stDataFinal;

    /**
         * @access Public
         * @param Object $valor
    */
    public function setFiltro($valor) { $this->stFiltro                                      = $valor; }
    public function setExercicio($valor) { $this->inExercicio                                   = $valor; }
    public function setDataInicial($valor) { $this->stDataInicial                                 = $valor; }
    public function setDataFinal($valor) { $this->stDataFinal                                   = $valor; }
    /**
         * @access Public
         * @param Object $valor
    */
    public function getFiltro() { return $this->stFiltro                                    ; }
    public function getExercicio() { return $this->inExercicio                                 ; }
    public function getDataInicial() { return $this->stDataInicial                               ; }
    public function getDataFinal() { return $this->stDataFinal                                 ; }

    /**
        * Método Construtor
        * @access Private
    */
    public function RContabilidadeRelatorioAnexo10()
    {
        $this->obROrcamentoEntidade                         = new ROrcamentoEntidade;
    }

    /**
        * Método abstrato
        * @access Public
    */
    public function geraRecordSet(&$rsRecordSet , $stOrder = "")
    {
        include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSomatorioReceitaOrcadaArrecadada.class.php" );
        $obFOrcamentoSomatorioReceitaOrcadaArrecadada = new FOrcamentoSomatorioReceitaOrcadaArrecadada;

        // Seta dados
        $obFOrcamentoSomatorioReceitaOrcadaArrecadada->setDado ( "exercicio"   , $this->inExercicio   );
        $obFOrcamentoSomatorioReceitaOrcadaArrecadada->setDado ( "data_inicial", $this->stDataInicial );
        $obFOrcamentoSomatorioReceitaOrcadaArrecadada->setDado ( "data_final"  , $this->stDataFinal   );
        $obFOrcamentoSomatorioReceitaOrcadaArrecadada->setDado ( "stFiltro"    , $this->stFiltro      );

        // executa sql e retorna Record Set
        $obErro = $obFOrcamentoSomatorioReceitaOrcadaArrecadada->recuperaTodos( $rsRecordSet );

    //    $obFOrcamentoSomatorioReceitaOrcadaArrecadada->debug();

        $inCount = 0;

        // formata record set
        $stGrupoVelho = 0;
        $stNivelVelho = 0;
        while ( !$rsRecordSet->eof() ) {
            $stGrupo = substr($rsRecordSet->getCampo('receita'),0,1);

            $arRecordSet[$inCount]['receita']       = $rsRecordSet->getCampo("receita");
            $arRecordSet[$inCount]['descricao']     = $rsRecordSet->getCampo("descricao");
            $arRecordSet[$inCount]['vl_orcado']     = number_format($rsRecordSet->getCampo("vl_orcado"), 2, ',', '.' );
            if (substr($rsRecordSet->getCampo('receita'),0,1) == 9) {
                $arRecordSet[$inCount]['vl_arrecadado'] = number_format($rsRecordSet->getCampo("vl_arrecadado") * -1, 2, ',', '.' );
                if ($rsRecordSet->getCampo("vl_orcado") > ($rsRecordSet->getCampo("vl_arrecadado")*-1)) {
                    $arRecordSet[$inCount]['vl_mais']       = number_format((($rsRecordSet->getCampo("vl_arrecadado")*-1)-$rsRecordSet->getCampo("vl_orcado")), 2, ',', '.' );
                } else {
                    $arRecordSet[$inCount]['vl_mais']       = '0,00';
                }

                if ($rsRecordSet->getCampo("vl_orcado") < $rsRecordSet->getCampo("vl_arrecadado")*-1) {
                    $arRecordSet[$inCount]['vl_menos']      = number_format(($rsRecordSet->getCampo("vl_orcado") - ($rsRecordSet->getCampo("vl_arrecadado")*-1)), 2, ',', '.' );
                } else {
                    $arRecordSet[$inCount]['vl_menos']      = '0,00';
                }
            } else {
                $arRecordSet[$inCount]['vl_arrecadado'] = number_format(($rsRecordSet->getCampo("vl_arrecadado") * -1), 2, ',', '.' );
                if ($rsRecordSet->getCampo("vl_orcado") < ($rsRecordSet->getCampo("vl_arrecadado")*-1)) {
                    $arRecordSet[$inCount]['vl_mais']       = number_format(($rsRecordSet->getCampo("vl_arrecadado")+$rsRecordSet->getCampo("vl_orcado"))*-1, 2, ',', '.' );
                } else {
                    $arRecordSet[$inCount]['vl_mais']       = '0,00';
                }

                if ($rsRecordSet->getCampo("vl_orcado") > ($rsRecordSet->getCampo("vl_arrecadado")*-1)) {
                    $arRecordSet[$inCount]['vl_menos']      = number_format(($rsRecordSet->getCampo("vl_orcado")+$rsRecordSet->getCampo("vl_arrecadado")), 2, ',', '.' );
                } else {
                    $arRecordSet[$inCount]['vl_menos']      = '0,00';
                }
            }

            if ($stGrupoVelho <> $stGrupo) {
                $stGrupoVelho = $stGrupo;
                $vlTotalOrcado      += str_replace(',','.',str_replace('.','',$arRecordSet[$inCount]['vl_orcado']));
                $vlTotalArrecadacao += str_replace(',','.',str_replace('.','',$arRecordSet[$inCount]['vl_arrecadado']));
            }
            if ($rsRecordSet->getCampo("nivel") > $stNivelVelho) {
                $somaMais        = str_replace(',','.',str_replace('.','',$arRecordSet[$inCount]['vl_mais']));
                $somaMenos       = str_replace(',','.',str_replace('.','',$arRecordSet[$inCount]['vl_menos']));
            } elseif ($rsRecordSet->getCampo("nivel") <= $stNivelVelho) {
                $vlTotalMais     += $somaMais;
                $vlTotalMenos    += $somaMenos;
                $somaMais        = str_replace(',','.',str_replace('.','',$arRecordSet[$inCount]['vl_mais']));
                $somaMenos       = str_replace(',','.',str_replace('.','',$arRecordSet[$inCount]['vl_menos']));
            }

            $stNivelVelho = $rsRecordSet->getCampo("nivel");

            $inCountVelho = $inCount;

            $inCount++;
            $rsRecordSet->proximo();

        }
        $vlTotalMais     += str_replace(',','.',str_replace('.','',$arRecordSet[$inCountVelho]['vl_mais']));
        $vlTotalMenos    += str_replace(',','.',str_replace('.','',$arRecordSet[$inCountVelho]['vl_menos']));

        $arRecordSet[$inCount]['receita']       = "";
        $arRecordSet[$inCount]['descricao']     = "";
        $arRecordSet[$inCount]['vl_orcado']     = "";
        $arRecordSet[$inCount]['vl_arrecadado'] = "";
        $arRecordSet[$inCount]['vl_mais']       = "";
        $arRecordSet[$inCount]['vl_menos']      = "";

        $inCount++;

        $arRecordSet[$inCount]['receita']       = "";
        $arRecordSet[$inCount]['descricao']     = "TOTAL GERAL";
        $arRecordSet[$inCount]['vl_orcado']     = number_format($vlTotalOrcado, 2, ',', '.' );;
        $arRecordSet[$inCount]['vl_arrecadado'] = number_format($vlTotalArrecadacao, 2, ',', '.' );;
        $arRecordSet[$inCount]['vl_mais']       = number_format($vlTotalMais, 2, ',', '.' );;
        $arRecordSet[$inCount]['vl_menos']      = number_format($vlTotalMenos, 2, ',', '.' );;

        $inCount++;

        $inCountAr = $inCount;
        $inEntidades = str_replace("'","",$this->stFiltro );
        $arEntidades = explode(",",$inEntidades );
        foreach ($arEntidades as $key => $inCodEntidade) {
            $this->obROrcamentoEntidade->setExercicio( $this->inExercicio );
            $this->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
            $this->obROrcamentoEntidade->consultarNomes($rsLista);
            if ($inCount == $inCountAr) {
                $arRecordSet[$inCount]['descricao'] = "";
                $inCount++;
                $arRecordSet[$inCount]['descricao'] = "ENTIDADES RELACIONADAS";
                $inCount++;
            }
                $arRecordSet[$inCount]['descricao'] = $rsLista->getCampo("entidade");

            $inCount++;
        }
         $rsRecordSetNovo = new RecordSet;
         $rsRecordSetNovo->preenche( $arRecordSet );
         $rsRecordSet = $rsRecordSetNovo;

        return $obErros;
    }
}
