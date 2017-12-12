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
    * Classe de regra de relatório para o Demonstrativo de Despesa - Anexo 11
    * Data de Criação: 12/05/2005

    * @author Analista: Gelson Wolowski
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Regra

    * $Id: RContabilidadeRelatorioAnexo11.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO     );
include_once( CAM_GF_CONT_MAPEAMENTO."FContabilidadeAnexo11.class.php"    );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );

class RContabilidadeRelatorioAnexo11 extends PersistenteRelatorio
{

    /**
        * @var Object
        * @access Private
    */
    public $obFContabilidadeAnexo11;
    /**
        * @var Object
        * @access Private
    */
    public $obRContabilidadeLancamentoValor;
    /**
        * @var String
        * @access Private
    */
    public $stFiltro;
    /**
        * @var String
        * @access Private
    */
    public $stDtInicial;
    /**
        * @var String
        * @access Private
    */
    public $stDtFinal;
    /**
        * @var String
        * @access Private
    */
    public $stEstilo;
    /**
        * @var Integer
        * @access Private
    */
    public $inCodEntidade;
    /**
        * @var Integer
        * @access Private
    */
    public $inExercicio;

    /**
        * @var String
        * @access Private
    */
    public $stSituacao;

    /**
         * @access Public
         * @param Object $valor
    */
    public function setFContabilidadeAnexo11($valor) { $this->obFContabilidadeAnexo11            = $valor; }
    /**
         * @access Public
         * @param String $valor
    */
    public function setFiltro($valor) { $this->stFiltro                           = $valor; }
    /**
         * @access Public
         * @param String $valor
    */
    public function setDtInicial($valor) { $this->stDtInicial                        = $valor; }
    /**
         * @access Public
         * @param String $valor
    */
    public function setDtFinal($valor) { $this->stDtFinal                          = $valor; }
    /**
         * @access Public
         * @param String $valor
    */
    public function setEstilo($valor) { $this->stEstilo                           = $valor; }
    /**
         * @access Public
         * @param Object $valor
    */
    public function setCodEntidade($valor) { $this->inCodEntidade                      = $valor; }
    /**
         * @access Public
         * @param Integer $valor
    */
    public function setExercicio($valor) { $this->inExercicio                        = $valor; }

    /**
         * @access Public
         * @param String $valor
    */
    public function setSituacao($valor) { $this->stSituacao		               		= $valor; }

    /**
         * @access Public
         * @param Object $valor
    */
    public function getFContabilidadeAnexo11() { return $this->obFContabilidadeAnexo11            ; }
    /**
         * @access Public
         * @param String $valor
    */
    public function getFiltro() { return $this->stFiltro                          ; }
    /**
         * @access Public
         * @param String $valor
    */
    public function getDtInicial() { return $this->stDtInicial                       ; }
    /**
         * @access Public
         * @param String $valor
    */
    public function getDtFinal() { return $this->stDtFinal                         ; }
    /**
         * @access Public
         * @param String $valor
    */
    public function getEstilo() { return $this->stEstilo                          ; }
    /**
         * @access Public
         * @param Object $valor
    */
    public function getCodEntidade() { return $this->inCodEntidade                     ; }
    /**
         * @access Public
         * @param Integer $valor
    */
    public function getExercicio() { return $this->inExercicio                       ; }

    /**
         * @access Public
         * @param String $valor
    */
    public function getSituacao() { return $this->stSituacao            	 			; }

    /**
        * Método Construtor
        * @access Private
    */
    public function RContabilidadeRelatorioAnexo11()
    {
        $this->obFContabilidadeAnexo11         = new FContabilidadeAnexo11;
        $this->obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;
        $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setExercicio     ( Sessao::getExercicio() );
        $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
    }

    /**
        * Método abstrato
        * @access Public
    */
    public function geraRecordSet(&$rsRecordSet, $stOrder = "")
    {
        $arRecordSet = array();

        $this->obFContabilidadeAnexo11->setDado( "exercicio"   		, $this->inExercicio );
        $this->obFContabilidadeAnexo11->setDado( "stFiltro"    		, $this->stFiltro    );
        $this->obFContabilidadeAnexo11->setDado( "stDtInicial" 		, $this->stDtInicial );
        $this->obFContabilidadeAnexo11->setDado( "stDtFinal"   		, $this->stDtFinal   );
        $this->obFContabilidadeAnexo11->setDado( "chEstilo"    		, $this->stEstilo    );
        $this->obFContabilidadeAnexo11->setDado( "stSituacao"		, $this->stSituacao  );

        $this->obFContabilidadeAnexo11->setDado( "stEntidades"  , $stEntidades );
        $obErro = $this->obFContabilidadeAnexo11->recuperaTodos( $rsRecordSet, "" , "" );

        $inCount = 0;
        $inFirstLoop = true;
        $arRecord = array();

        $nuTotalCreditoOrcamentario = 0;
        $nuTotalCreditoEspecial     = 0;
        $nuTotal                    = 0;
        $nuTotalRealizado           = 0;
        $nuTotalDiferenca           = 0;

        while ( !$rsRecordSet->eof() ) {

            if ($rsRecordSet->getCampo('vl_credito_orcamentario') <> "0.00" OR $rsRecordSet->getCampo('vl_credito_especial') <> "0.00" OR $rsRecordSet->getCampo('vl_realizado') <> "0.00" OR $rsRecordSet->getCampo('vl_original') <> "0.00") {

                if( ( $rsRecordSet->getCampo('nivel') == 1  AND $inFirstLoop == false ) OR
                    ( $rsRecordSet->getCampo('nivel') == 2  AND $inCodNivelAnterior != 1 ) ){
                    $arRecord[$inCount]['nivel']                   = "";
                    $arRecord[$inCount]['descricao']               = "";
                    $arRecord[$inCount]['vl_credito_orcamentario'] = "";
                    $arRecord[$inCount]['vl_credito_especial']     = "";
                    $arRecord[$inCount]['vl_total']                = "";
                    $arRecord[$inCount]['vl_realizado']            = "";
                    $arRecord[$inCount++]['vl_diferenca']          = "";
                }

                $arDespesa = explode( "." , $rsRecordSet->getCampo('cod_estrutural') );

                $nuValorTotal = $rsRecordSet->getCampo('vl_credito_orcamentario') + $rsRecordSet->getCampo('vl_credito_especial');
                if (!$nuValorTotal) {
                    $nuValorTotal = '0.00';
                }
                $nuValorDif = $nuValorTotal - $rsRecordSet->getCampo('vl_realizado');
                if (!$nuValorDif) {
                    $nuValorDif = '0.00';
                }

                $arRecord[$inCount]['nivel']                   = $rsRecordSet->getCampo('nivel');
                $arRecord[$inCount]['descricao']               = $rsRecordSet->getCampo('descricao');
                $arRecord[$inCount]['vl_credito_orcamentario'] = $rsRecordSet->getCampo('vl_credito_orcamentario');
                $arRecord[$inCount]['vl_credito_especial']     = $rsRecordSet->getCampo('vl_credito_especial');
                $arRecord[$inCount]['vl_total']                = $nuValorTotal;
                $arRecord[$inCount]['vl_realizado']            = $rsRecordSet->getCampo('vl_realizado');
                $arRecord[$inCount]['vl_diferenca']            = $nuValorDif;

                if ($rsRecordSet->getCampo('nivel')==1) {
                    $nuTotalCreditoOrcamentario += $arRecord[$inCount]['vl_credito_orcamentario'];
                    $nuTotalCreditoEspecial     = bcadd( $nuTotalCreditoEspecial, $arRecord[$inCount]['vl_credito_especial'], 2);
                    $nuTotal                    += $arRecord[$inCount]['vl_total'];
                    $nuTotalRealizado           = bcadd( $nuTotalRealizado, $arRecord[$inCount]['vl_realizado'], 2);
                    $nuTotalDiferenca           += $arRecord[$inCount]['vl_diferenca'];
                }

                $inCount++;

                if ( $rsRecordSet->getCampo('nivel') == 1 OR $rsRecordSet->getCampo('nivel') == 2 ) {
                    $arRecord[$inCount]['nivel']                   = "";
                    $arRecord[$inCount]['descricao']               = "";
                    $arRecord[$inCount]['vl_credito_orcamentario'] = "";
                    $arRecord[$inCount]['vl_credito_especial']     = "";
                    $arRecord[$inCount]['vl_total']                = "";
                    $arRecord[$inCount]['vl_realizado']            = "";
                    $arRecord[$inCount++]['vl_diferenca']          = "";
                }
                $inCodNivelAnterior = $rsRecordSet->getCampo('nivel');
                $inFirstLoop = false;

            }
            $rsRecordSet->proximo();
        }

        //MONTA TOTAL GERAL
        $arRecord[$inCount]['nivel']                   = "";
        $arRecord[$inCount]['descricao']               = "";
        $arRecord[$inCount]['vl_credito_orcamentario'] = "";
        $arRecord[$inCount]['vl_credito_especial']     = "";
        $arRecord[$inCount]['vl_total']                = "";
        $arRecord[$inCount]['vl_realizado']            = "";
        $arRecord[$inCount]['vl_diferenca']          = "";

        $inCount++;

        $arRecord[$inCount]['nivel']                   = 1;
        $arRecord[$inCount]['descricao']               = 'TOTAL GERAL';
        $arRecord[$inCount]['vl_credito_orcamentario'] = $nuTotalCreditoOrcamentario;
        $arRecord[$inCount]['vl_credito_especial']     = $nuTotalCreditoEspecial;
        $arRecord[$inCount]['vl_total']                = $nuTotal;
        $arRecord[$inCount]['vl_realizado']            = $nuTotalRealizado;
        $arRecord[$inCount]['vl_diferenca']            = $nuTotalDiferenca;

        $inCount++;

        //MONTA ENTIDADES RELACIONADAS
        $arRecord[$inCount]['nivel']                   = "";
        $arRecord[$inCount]['descricao']               = "";
        $arRecord[$inCount]['vl_credito_orcamentario'] = "";
        $arRecord[$inCount]['vl_credito_especial']     = "";
        $arRecord[$inCount]['vl_total']                = "";
        $arRecord[$inCount]['vl_realizado']            = "";
        $arRecord[$inCount]['vl_diferenca']          = "";

        $inCount++;

        $arRecord[$inCount]['nivel']                   = 1;
        $arRecord[$inCount]['descricao']               = 'ENTIDADES RELACIONADAS';
        $arRecord[$inCount]['vl_credito_orcamentario'] = '';
        $arRecord[$inCount]['vl_credito_especial']     = '';
        $arRecord[$inCount]['vl_total']                = '';
        $arRecord[$inCount]['vl_realizado']            = '';
        $arRecord[$inCount]['vl_diferenca']          = '';

        $inEntidades = str_replace( " " , "" , $this->getCodEntidade() );
        $arEntidades = explode( "," , $inEntidades );

        foreach ($arEntidades as $key => $inCodEntidade) {
            $inCount++;

            $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
            $this->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->consultarNomes( $rsLista );

            $arRecord[$inCount]['nivel']                   = 1;
            $arRecord[$inCount]['descricao']               = $rsLista->getCampo('entidade');
            $arRecord[$inCount]['vl_credito_orcamentario'] = '';
            $arRecord[$inCount]['vl_credito_especial']     = '';
            $arRecord[$inCount]['vl_total']                = '';
            $arRecord[$inCount]['vl_realizado']            = '';
            $arRecord[$inCount]['vl_diferenca']            = '';
        }

        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche( $arRecord );

        $rsRecordSet->addFormatacao( "vl_credito_orcamentario" , "CONTABIL" );
        $rsRecordSet->addFormatacao( "vl_credito_especial"     , "CONTABIL" );
        $rsRecordSet->addFormatacao( "vl_total"                , "CONTABIL" );
        $rsRecordSet->addFormatacao( "vl_realizado"            , "CONTABIL" );
        $rsRecordSet->addFormatacao( "vl_diferenca"            , "CONTABIL" );

        return $obErro;
    }
}
