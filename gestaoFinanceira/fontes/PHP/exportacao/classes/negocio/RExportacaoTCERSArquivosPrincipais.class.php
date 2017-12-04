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
    * Classe de Exportação Arquivos Principais

    * Data de Criação   : 01/02/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Lucas Texeira Stephanou

    * @package URBEM
    * @subpackage Exportador

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.01
*/

/*
$Log$
Revision 1.5  2006/07/05 20:46:04  cleisson
Adicionada tag Log aos arquivos

*/

/* Includes */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoOrgao.class.php"                );
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoUnidade.class.php"              );
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoDespesa.class.php"              );
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoTCERSExportacaoBalanceteReceita.class.php");
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoTCERSExportacaoReceita.class.php"       );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php"  );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php"       );
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoPagamento.class.php"           );
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoLiquidacao.class.php"          );
include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php"                );
include_once( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php"                         );
include_once( CAM_GF_EXP_MAPEAMENTO."FExportacaoTCERSBalanceteDespesa.class.php"                         );

/**
    * Classe de Regra para geração de arquivo principais para o ExportacaoTCE-RS

    * @author   Desenvolvedor :   Diego Barbosa Victoria
    * @author   Analista      :   Lucas Teixeira Stephanou
*/
class RExportacaoTcersArquivosPrincipais
{
    /* Valores entre*/
    public $inPeriodo      ;
    public $stCodEntidades ;
    public $inCodOrgao     ;
    public $stDataInicial  ;
    public $stDataFinal    ;
    public $stExercicio    ;
    public $arArquivos = array()     ;
    /* Objetos de Tabela */
    /*var $obOrcamentoDespesa         =   new TOrcamentoDespesa()         ;
    public $obOrcamentoContaDespesa    =   new TOrcamentoContaDespesa()    ;
    public $obEmpenhoEmpenho           =   new TEmpenhoEmpenho()           ;
    public $obEmpenhoPreEmpenho        =   new TEmpenhoPreEmpenho()        ;
    public $obEmpenhoHistorico         =   new TEmpenhoHistoricoEmpenho()  ;
    public $obEmpenhoNotaLiquidacao    =   new TEmpenhoNotaLiquidacao()    ;
    public $obOrcamentoContaReceita    =   new TOrcamentoContaReceita()    ;*/
    public $obFExportacaoTCERSExportacaoBalanceteReceita  ;
    public $obFExportacaoTCERSExportacaoReceita           ;
    public $obTContabilidadeValorLancamento     ;
    public $obTContabilidadePlanoConta          ;
    public $obFExportacaoPagamento              ;
    public $obFExportacaoLiquidacao             ;
    public $obTNorma                            ;
    public $obFExportacaoBalanceteDespesa       ;

    /**
    * Metodo Construtor
    * @access Private
    */
    public function RExportacaoTcersArquivosPrincipais()
    {
        $this->obFExportacaoTCERSExportacaoBalanceteReceita   =   new FExportacaoTCERSExportacaoBalanceteReceita() ;
        $this->obFExportacaoTCERSExportacaoReceita           =   new  FExportacaoTCERSExportacaoReceita() ;
        $this->obTContabilidadeValorLancamento      =   new TContabilidadeValorLancamento() ;
        $this->obTContabilidadePlanoConta           =   new TContabilidadePlanoConta();
        $this->obTEmpenhoEmpenho                    =   new TEmpenhoEmpenho;
        $this->obFExportacaoPagamento               =   new FExportacaoPagamento();
        $this->obFExportacaoLiquidacao              =   new FExportacaoLiquidacao();
        $this->obTNorma                             =   new TNorma();
        $this->obFExportacaoBalanceteDespesa        =   new FExportacaoTCERSBalanceteDespesa();

    }
    // SETANDO
    public function setPeriodo($valor) {   $this->inPeriodo        =   $valor; }
    public function setCodEntidades($valor) {   $this->stCodEntidades   =   $valor; }
    public function setExercicio($valor) {   $this->stExercicio      =   $valor; }
    public function setCodOrgao($valor) {   $this->inCodOrgao       =   $valor; }
    public function setArquivos($valor) {   $this->arArquivos       =   $valor; }
    public function setDataInicial($valor) {   $this->stDataInicial    =   $valor; }
    public function setDataFinal($valor) {   $this->stDataFinal      =   $valor; }

    // GETANDO
    public function getPeriodo() {   return $this->inPeriodo     ;   }
    public function getCodEntidades() {   return $this->stCodEntidades;   }
    public function getExercicio() {   return $this->stExercicio   ;   }
    public function getCodOrgao() {   return $this->inCodOrgao    ;   }
    public function getArquivos() {   return $this->arArquivos    ;   }
    public function getDataInicial() {   return $this->stDataInicial ;   }
    public function getDataFinal() {   return $this->stDataFinal   ;   }

    // Gerando Recordset
    public function geraRecordset(&$arRecordset)
    {

        if (in_array("EMPENHO.TXT",$this->getArquivos())) {
            $this->obTEmpenhoEmpenho->setDado('stExercicio'     , $this->getExercicio()             );
            $this->obTEmpenhoEmpenho->setDado('dtInicial'       , $this->getDataInicial()           );
            $this->obTEmpenhoEmpenho->setDado('dtFinal'         , $this->getDataFinal()             );
            $this->obTEmpenhoEmpenho->setDado('stCodEntidades'  , $this->getCodEntidades()          );
            $obErro     =   $this->obTEmpenhoEmpenho->recuperaDadosExportacao($rsRecordset          );
            $arRecordset["EMPENHO.TXT"] = $rsRecordset;

        }
        if (in_array("BAL_REC.TXT",$this->getArquivos())) {
            $this->obFExportacaoTCERSExportacaoBalanceteReceita->setDado('stExercicio'   , $this->getExercicio()         );
            $this->obFExportacaoTCERSExportacaoBalanceteReceita->setDado('stCodEntidades', $this->getCodEntidades()      );
            $this->obFExportacaoTCERSExportacaoBalanceteReceita->setDado('dtInicial'     , $this->getDataInicial()       );
            $this->obFExportacaoTCERSExportacaoBalanceteReceita->setDado('dtFinal'       , $this->getDataFinal()         );
            $obErro     =   $this->obFExportacaoTCERSExportacaoBalanceteReceita->recuperaDadosExportacao($rsRecordset    );
            $arRecordset["BAL_REC.TXT"] = $rsRecordset;

        }
        if (in_array("RECEITA.TXT",$this->getArquivos())) {
            $this->obFExportacaoTCERSExportacaoReceita->setDado('stExercicio'     , $this->getExercicio()               );
            $this->obFExportacaoTCERSExportacaoReceita->setDado('stCodEntidades'  , $this->getCodEntidades()            );
            $this->obFExportacaoTCERSExportacaoReceita->setDado('inBimestre'      , $this->getPeriodo()                 );
            $obErro     =   $this->obFExportacaoTCERSExportacaoReceita->recuperaDadosExportacao($rsRecordset );
            $arRecordset["RECEITA.TXT"] = $rsRecordset;
        }
        if (in_array("RD_EXTRA.TXT",$this->getArquivos())) {
            $this->obTContabilidadeValorLancamento->setDado('stExercicio'       , $this->getExercicio()     );
            $this->obTContabilidadeValorLancamento->setDado('stCodEntidades'    , $this->getCodEntidades()  );
            $obErro =   $this->obTContabilidadeValorLancamento->recuperaDadosExportacao($rsRecordset        );
            $arRecordset["RD_EXTRA.TXT"] = $rsRecordset;
        }
        if (in_array("BAL_VER.TXT",$this->arArquivos)) {
            $this->obTContabilidadePlanoConta->setDado('stExercicio'        , $this->getExercicio()             );
            $this->obTContabilidadePlanoConta->setDado('dtInicial'          , $this->getDataInicial()           );
            $this->obTContabilidadePlanoConta->setDado('dtFinal'            , $this->getDataFinal()             );
            $this->obTContabilidadePlanoConta->setDado('stCodEntidades'     , $this->getCodEntidades()          );
            $obErro =   $this->obTContabilidadePlanoConta->recuperaDadosExportacaoBalVerificacao($rsRecordset   );
            $arRecordset["BAL_VER.TXT"] = $rsRecordset;
        }
        if (in_array("PAGAMENT.TXT",$this->arArquivos)) {
            $this->obFExportacaoPagamento->setDado('stExercicio'    , $this->getExercicio()     );
            $this->obFExportacaoPagamento->setDado('dtInicial'      , $this->getDataInicial()   );
            $this->obFExportacaoPagamento->setDado('dtFinal'        , $this->getDataFinal()     );
            $this->obFExportacaoPagamento->setDado('stCodEntidades' , $this->getCodEntidades()  );
            $obErro =   $this->obFExportacaoPagamento->recuperaTodos($rsRecordset               );
            $arRecordset["PAGAMENT.TXT"] = $rsRecordset;
        }
        if (in_array("LIQUIDAC.TXT",$this->arArquivos)) {
            $this->obFExportacaoLiquidacao->setDado('stExercicio'    , $this->getExercicio()        );
            $this->obFExportacaoLiquidacao->setDado('dtInicial'      , $this->getDataInicial()      );
            $this->obFExportacaoLiquidacao->setDado('dtFinal'        , $this->getDataFinal()        );
            $this->obFExportacaoLiquidacao->setDado('stCodEntidades' , $this->getCodEntidades()     );
            $this->obFExportacaoLiquidacao->setDado('stFiltro'       , ''                           );
            $obErro =   $this->obFExportacaoLiquidacao->recuperaDadosExportacao($rsRecordset        );

            $inCount=0;
            while ( !$rsRecordset->eof()) {
                $arRecordsetNovo[$inCount]['exercicio'] = $rsRecordset->getCampo('exercicio');
                $arRecordsetNovo[$inCount]['cod_empenho'] = $rsRecordset->getCampo('cod_empenho');
                $arRecordsetNovo[$inCount]['cod_entidade'] = $rsRecordset->getCampo('cod_entidade');
                $arRecordsetNovo[$inCount]['cod_nota'] = $rsRecordset->getCampo('cod_nota');
                $arRecordsetNovo[$inCount]['data_pagamento'] = $rsRecordset->getCampo('data_pagamento');
                $arRecordsetNovo[$inCount]['valor_liquidacao'] = $rsRecordset->getCampo('valor_liquidacao');
                $arRecordsetNovo[$inCount]['sinal_valor'] = $rsRecordset->getCampo('sinal_valor');
                $arRecordsetNovo[$inCount]['observacao'] = $rsRecordset->getCampo('observacao');
                $arRecordsetNovo[$inCount]['ordem'] = $rsRecordset->getCampo('ordem');
                $arRecordsetNovo[$inCount]['codigo_operacao'] =  $rsRecordset->getCampo('codigo_operacao');
                $arRecordsetNovo[$inCount]['zero'] = "000000000000000000000000000000";

                $rsRecordset->proximo();
                $inCount++;
            }

            $rsRecordset = new RecordSet;
            $rsRecordset->preenche( $arRecordsetNovo );
            $arRecordset["LIQUIDAC.TXT"] = $rsRecordset;
        }
        if (in_array("DECRETO.TXT",$this->arArquivos)) {
            $this->obTNorma->setDado('stExercicio'    , $this->getExercicio()        );
            $this->obTNorma->setDado('dtInicial'      , $this->getDataInicial()      );
            $this->obTNorma->setDado('dtFinal'        , $this->getDataFinal()        );
            $this->obTNorma->setDado('stFiltro'       , ''                           );
            $this->obTNorma->setDado('stCodEntidades' , $this->getCodEntidades()     );
            $obErro =   $this->obTNorma->recuperaDadosExportacao($rsRecordset        );
            $arRecordset["DECRETO.TXT"] = $rsRecordset;
        }
        if (in_array("BAL_DESP.TXT",$this->arArquivos)) {
            $this->obFExportacaoBalanceteDespesa->setDado('stExercicio'    , $this->getExercicio()        );
            $this->obFExportacaoBalanceteDespesa->setDado('dtInicial'      , $this->getDataInicial()      );
            $this->obFExportacaoBalanceteDespesa->setDado('dtFinal'        , $this->getDataFinal()        );
            $this->obFExportacaoBalanceteDespesa->setDado('stCodEntidades' , $this->getCodEntidades()     );
            $obErro =   $this->obFExportacaoBalanceteDespesa->recuperaDadosExportacao($rsRecordset        );
            $arRecordset["BAL_DESP.TXT"] = $rsRecordset;
        }

        return $obErro;
    }
}
