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
    * Classe de regra de relatório para o balancete patrimonial
    * Data de Criação   : 15/05/2005

    * @author Desenvolvedor: Marcelo B. Paulino
    * @author Analista: Gelson

    * @package URBEM
    * @subpackage Regra

    * $Id: RContabilidadeRelatorioAnexo14.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO             );
include_once( CAM_GF_CONT_MAPEAMENTO."FContabilidadeBalancoPatrimonial.class.php" );

class RContabilidadeRelatorioAnexo14 extends PersistenteRelatorio
{

    /**
        * @var Object
        * @access Private
    */
    public $obFContabilidadeBalancoPatrimonial;
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
    public $inExercicio;

    /**
         * @access Public
         * @param Object $valor
    */

    public $inTipoRelatorio;

    /**
         * @access Public
         * @param Object $valor
    */

    public function setFContabilidadeBalancoPatrimonial($valor) { $this->obFContabilidadeBalancoPatrimonial = $valor; }
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
         * @param Integer $valor
    */
    public function setExercicio($valor) { $this->inExercicio                        = $valor; }

    /**
         * @access Public
         * @param Integer $valor
    */
    public function setTipoRelatorio($valor) { $this->inTipoRelatorio                    = $valor; }

    /**
         * @access Public
         * @param Object $valor
    */
    public function getFContabilidadeBalancoPatrimonial() { return $this->obFContabilidadeBalancoPatrimonial; }
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
         * @param Integer $valor
    */
    public function getExercicio() { return $this->inExercicio                       ; }

    /**
         * @access Public
         * @param Integer $valor
    */
    public function getTipoRelatorio() { return $this->inTipoRelatorio                   ; }

    /**
        * Método Construtor
        * @access Private
    */
    public function RContabilidadeRelatorioAnexo14()
    {
        $this->obFContabilidadeBalancoPatrimonial = new FContabilidadeBalancoPatrimonial;
    }

    /**
        * Método abstrato
        * @access Public
    */
    public function geraRecordSet(&$rsRecordSet, $stOrder = "")
    {
        $arRecordSet = array();

        $this->obFContabilidadeBalancoPatrimonial->setDado( "exercicio"   , $this->inExercicio );
        $this->obFContabilidadeBalancoPatrimonial->setDado( "stFiltro"    , $this->stFiltro    );
        $this->obFContabilidadeBalancoPatrimonial->setDado( "stDtInicial" , $this->stDtInicial );
        $this->obFContabilidadeBalancoPatrimonial->setDado( "stDtFinal"   , $this->stDtFinal   );
        $this->obFContabilidadeBalancoPatrimonial->setDado( "chEstilo"    , $this->stEstilo    );

        $obErro = $this->obFContabilidadeBalancoPatrimonial->recuperaTodos( $rsRecordSet, "", "" );
        //$this->obFContabilidadeBalancoPatrimonial->debug(); die();

        $inCount = 0;

        $arRecord = array();
        $arRecordAP = array();

        $boCabecalhoAtFinanceiro     = false;
        $boCabecalhoAtPermanente     = false;
        $boCabecalhoAtCompensado     = false;
        $boCabecalhoPsCompensado     = false;
        $boCabecalhoPsFinanceiro     = false;
        $boCabecalhoPsPermanente     = false;
        $boCabecalhoSaldoPatrimonial = false;

        $flTotalAtFinanceiro     = 0;
        $flTotalAtPermanente     = 0;
        $flTotalAtCompensado     = 0;
        $flTotalPsCompensado     = 0;
        $flTotalPsFinanceiro     = 0;
        $flTotalPsPermanente     = 0;
        $flTotalAtivoReal        = 0;
        $flTotalPassivoReal      = 0;
        $flTotalSaldoPatrimonial = 0;

        $arRecord[$inCount]['nivel'] = 1;
        $arRecord[$inCount]['nom_conta'] = 'ATIVO';
        $arRecord[$inCount]['saldo_atual'] = '';
        $arRecord[$inCount]['nom_conta_passivo'] = 'PASSIVO';
        $arRecord[$inCount]['saldo_atual_passivo'] = '';
        $inCount++;

        // Seta cod_sistema para contas que estão como não infomardas
        while ( !$rsRecordSet->eof() ) {
            if ( strtoupper($rsRecordSet->getCampo('nom_sistema')) == strtoupper('Não Informado')) {
                $stCodEstruturalReduzido = "";
                $arCodEstrutural = explode( '.', $rsRecordSet->getCampo('cod_estrutural') );
                for ( $x=0; $x<$rsRecordSet->getCampo('nivel'); $x++ ) {
                    $stCodEstruturalReduzido .= $arCodEstrutural[$x].'.';
                }
                $stCodEstruturalReduzido = ( $stCodEstruturalReduzido ) ? substr($stCodEstruturalReduzido, 0, strlen($stCodEstruturalReduzido)-1) : '';
                $inCorrente      = $rsRecordSet->getCorrente();
                $stCodEstrutural = $rsRecordSet->getCampo("cod_estrutural");
                while ( !$rsRecordSet->eof() ) {
                    if ( strpos( $rsRecordSet->getCampo('cod_estrutural'), $stCodEstruturalReduzido ) !== false ) {
                        if ( strtoupper($rsRecordSet->getCampo('nom_sistema')) != strtoupper('Não Informado') ) {
                            $arSistemaContabil[$stCodEstrutural][strtoupper($rsRecordSet->getCampo('nom_sistema'))] = bcadd( $arSistemaContabil[$stCodEstrutural][strtoupper($rsRecordSet->getCampo('nom_sistema'))],  $rsRecordSet->getCampo('vl_saldo_atual'), 4 );
                        }
                    } else break;
                    $rsRecordSet->proximo();
                }
                $rsRecordSet->setCorrente( $inCorrente );
            }
            $rsRecordSet->proximo();
        }
        $rsRecordSet->setPrimeiroElemento();

        // Se for selecionada uma entidade RPPS então seta o grupo em segmentos de imóveis como Financeiro
        $flVlInvestimentos = 0;
        $arFiltro = Sessao::read('filtroRelatorio');
        $inCodEntidadeRPPS = SistemaLegado::pegaDado('valor','administracao.configuracao'," WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 8 AND parametro = 'cod_entidade_rpps'");
        if ( in_array( $inCodEntidadeRPPS, $arFiltro['inCodEntidade'] ) ) {
            foreach ($arSistemaContabil as $stKey=>$stValue) {
                if (substr($stKey, 0, 7) == '1.1.5.3' ) {
                    $flValorTMP = 0;
                    if (isset($stValue['FINANCEIRO'])) {
                        $flValorTMP = $stValue['FINANCEIRO'];
                        unset($stValue['FINANCEIRO']);
                    }
                    if (isset($stValue['PATRIMONIAL'])) {
                        $flValorTMP += $stValue['PATRIMONIAL'];
                        unset($stValue['PATRIMONIAL']);
                    }

                    $stValue['FINANCEIRO'] = $flValorTMP;
                    $arSistemaContabil[$stKey] = $stValue;
                    $boEntidadeRPPS = true;

                } elseif (substr($stKey, 0, 5) == '1.1.5') {
                    $flVlInvestimentos += $stValue['PATRIMONIAL'];
                }
            }

            if ($arSistemaContabil['1.1.5.0.0.00.00.00.00.00']['PATRIMONIAL'] == $flVlInvestimentos) {
                unset($arSistemaContabil['1.1.5.0.0.00.00.00.00.00']['PATRIMONIAL']);
            }

        }

        $inCountAF = 0;
        $inCountAP = 0;

        while ( !$rsRecordSet->eof() ) {

            $arCodEstrutural = explode( "." , $rsRecordSet->getCampo('cod_estrutural') );
            $stCodEstrutural = $rsRecordSet->getCampo('cod_estrutural');

            //ATIVO
            if ($arCodEstrutural[0] == '1') {
                //ATIVO FINANCEIRO
                if ( strtoupper($rsRecordSet->getCampo('nom_sistema')) == strtoupper('Financeiro') or $arSistemaContabil[$stCodEstrutural][strtoupper('Financeiro')] or ($boEntidadeRPPS AND substr($stCodEstrutural, 0, 7) == '1.1.5.3')) {

                    $nuValor = ($arSistemaContabil[$stCodEstrutural][strtoupper('Financeiro')]) ? $arSistemaContabil[$stCodEstrutural][strtoupper('Financeiro')] : $rsRecordSet->getCampo('vl_saldo_atual');

                    if ($boCabecalhoAtFinanceiro == false) {
                        $arRecordAF[$inCountAF]['nivel'] = 2;
                        $arRecordAF[$inCountAF]['nom_conta'] = 'Ativo Financeiro';
                        $arRecordAF[$inCountAF]['saldo_atual'] = '';
                        $boCabecalhoAtFinanceiro = true;
                        $inCountAF++;
                    }

                    //TERCEIRO NIVEL
                    if ( $rsRecordSet->getCampo('nivel') == '3' ) {
                        $arRecordAF[$inCountAF]['nivel'] = 3;
                        $arRecordAF[$inCountAF]['nom_conta'] = $rsRecordSet->getCampo('nom_conta');
                        $arRecordAF[$inCountAF]['saldo_atual'] = '';
                        $inCountAF++;
                    }

    //                if ( $rsRecordSet->getCampo('vl_saldo_atual') != 0 ) {
                    if ($nuValor != 0) {
                        //QUARTO NIVEL
                        if ( $rsRecordSet->getCampo('nivel') == '4' ) {
                            $arRecordAF[$inCountAF]['nivel'] = 4;
                            $arRecordAF[$inCountAF]['nom_conta'] = $rsRecordSet->getCampo('nom_conta');
                            $arRecordAF[$inCountAF]['saldo_atual'] = $nuValor;

                            $flTotalAtFinanceiro += $nuValor;
                            $inUltimoQuartoNivelF = $inCountAF;
                            $inCountAF++;
                        }

                        //QUINTO NIVEL
                        if ( $rsRecordSet->getCampo('nivel') == '5' ) {
                            $arRecordAF[$inCountAF]['nivel'] = 5;
                            $arRecordAF[$inCountAF]['nom_conta'] = $rsRecordSet->getCampo('nom_conta');
                            $arRecordAF[$inCountAF]['saldo_atual'] = $nuValor;

                            $flTotalAtFinanceiro += $nuValor;
                            $flTotalAtFinanceiro -= $arRecordAF[$inUltimoQuartoNivelF]['saldo_atual'];
                            $arRecordAF[$inUltimoQuartoNivelF]['saldo_atual'] = '';
                            $inCountAF++;
                        }

                        // Se o Relatório for Analístico lista as contas a partir do nível 5
                        if ($this->inTipoRelatorio != 1) {
                            //SEXTO AO DECIMO NIVEL
                            if ($rsRecordSet->getCampo('nivel') > 5 && $rsRecordSet->getCampo('nivel') <= 10) {

                                    $arRecordAF[$inCountAF]['nivel'] = $rsRecordSet->getCampo('nivel');
                                    $arRecordAF[$inCountAF]['nom_conta'] = $rsRecordSet->getCampo('nom_conta');
                                    $arRecordAF[$inCountAF]['saldo_atual'] = $nuValor;

                                    $inCountAF++;
                            }

                        }

                    }

                }

                //ATIVO PERMANENTE
                if( (strtoupper($rsRecordSet->getCampo('nom_sistema')) == strtoupper('Patrimonial') AND $boEntidadeRPPS AND substr($stCodEstrutural, 0, 7) != '1.1.5.3')
                    OR (strtoupper($rsRecordSet->getCampo('nom_sistema')) == strtoupper('Patrimonial') AND !$boEntidadeRPPS)
                    OR $arSistemaContabil[$stCodEstrutural][strtoupper('Patrimonial')] ) {

                    $nuValor = ($arSistemaContabil[$stCodEstrutural][strtoupper('Patrimonial')]) ? $arSistemaContabil[$stCodEstrutural][strtoupper('Patrimonial')] : $rsRecordSet->getCampo('vl_saldo_atual');

                    if ($boCabecalhoAtPermanente == false) {

                        $arRecordAP[$inCountAP]['nivel'] = '';
                        $arRecordAP[$inCountAP]['nom_conta'] = '';
                        $arRecordAP[$inCountAP]['saldo_atual'] = '';
                        $inCountAP++;

                        $arRecordAP[$inCountAP]['nivel'] = 2;
                        $arRecordAP[$inCountAP]['nom_conta'] = 'Ativo Permanente';
                        $arRecordAP[$inCountAP]['saldo_atual'] = '';
                        $boCabecalhoAtPermanente = true;
                        $inCountAP++;
                    }

                    //TERCEIRO NIVEL
                    if ( $rsRecordSet->getCampo('nivel') == '3' ) {
                        $arRecordAP[$inCountAP]['nivel'] = 3;
                        $arRecordAP[$inCountAP]['nom_conta'] = $rsRecordSet->getCampo('nom_conta');
                        $arRecordAP[$inCountAP]['saldo_atual'] = '';
                        $inCountAP++;
                    }

    //                if ( $rsRecordSet->getCampo('vl_saldo_atual') != 0 ) {
                    if ($nuValor != 0) {
                        //QUARTO NIVEL
                        if ( $rsRecordSet->getCampo('nivel') == '4' ) {
                            $arRecordAP[$inCountAP]['nivel'] = 4;
                            $arRecordAP[$inCountAP]['nom_conta'] = $rsRecordSet->getCampo('nom_conta');
                            $arRecordAP[$inCountAP]['saldo_atual'] = $nuValor;

                            $flTotalAtPermanente += $nuValor;
                            $inUltimoQuartoNivelP = $inCountAP;
                            $inCountAP++;
                        }

                        //QUINTO NIVEL
                        if ( $rsRecordSet->getCampo('nivel') == '5' ) {
                            $arRecordAP[$inCountAP]['nivel'] = 5;
                            $arRecordAP[$inCountAP]['nom_conta'] = $rsRecordSet->getCampo('nom_conta');
                            $arRecordAP[$inCountAP]['saldo_atual'] = $nuValor;

                            $flTotalAtPermanente += $nuValor;
                            $flTotalAtPermanente = $flTotalAtPermanente - $arRecordAP[$inUltimoQuartoNivelP]['saldo_atual'];
                            $arRecordAP[$inUltimoQuartoNivelP]['saldo_atual'] = '';
                            $inCountAP++;
                        }

                        // Se o Relatório for Analístico lista as contas a partir do nível 5
                        if ($this->inTipoRelatorio != 1) {
                            //SEXTO AO DECIMO NIVEL
                            if ($rsRecordSet->getCampo('nivel') > 5 && $rsRecordSet->getCampo('nivel') <= 10) {

                                $arRecordAP[$inCountAP]['nivel'] = $rsRecordSet->getCampo('nivel');
                                $arRecordAP[$inCountAP]['nom_conta'] = $rsRecordSet->getCampo('nom_conta');
                                $arRecordAP[$inCountAP]['saldo_atual'] = $nuValor;

                                $inCountAP++;

                            }

                        }

                    }

                }

            }
            $rsRecordSet->proximo();
        }

        $rsRecordSet->setPrimeiroElemento();
        $inCount   = 1;
        $inCountAF = 0;
        $inCountAP = 0;

        while ( !$rsRecordSet->eof() ) {

            $arCodEstrutural = explode( "." , $rsRecordSet->getCampo('cod_estrutural') );
            $stCodEstrutural = $rsRecordSet->getCampo('cod_estrutural');

            //PASSIVO
            if ($arCodEstrutural[0].'.'.$arCodEstrutural[1] == '2.1' OR $arCodEstrutural[0].'.'.$arCodEstrutural[1] == '2.2') {

                //PASSIVO FINANCEIRO
                if ( strtoupper($rsRecordSet->getCampo('nom_sistema')) == strtoupper('Financeiro') OR $arSistemaContabil[$stCodEstrutural][strtoupper('Financeiro')]) {

                    $nuValor = ($arSistemaContabil[$stCodEstrutural][strtoupper('Financeiro')]) ? $arSistemaContabil[$stCodEstrutural][strtoupper('Financeiro')] : $rsRecordSet->getCampo('vl_saldo_atual');

                    if ($boCabecalhoPsFinanceiro == false) {
                        $arRecordAF[$inCountAF]['nivel_passivo'] = 2;
                        $arRecordAF[$inCountAF]['nom_conta_passivo'] = 'Passivo Financeiro';
                        $arRecordAF[$inCountAF]['saldo_atual_passivo'] = '';
                        $boCabecalhoPsFinanceiro = true;
                        $inCountAF++;
                    }

                    //TERCEIRO NIVEL
                    if ( $rsRecordSet->getCampo('nivel') == '3' ) {
                        $arRecordAF[$inCountAF]['nivel_passivo'] = 3;
                        $arRecordAF[$inCountAF]['nom_conta_passivo'] = $rsRecordSet->getCampo('nom_conta');
                        $arRecordAF[$inCountAF]['saldo_atual_passivo'] = '';
                        $inCountAF++;
                    }

                    if ($nuValor != 0) {
                        //QUARTO NIVEL
                        if ( $rsRecordSet->getCampo('nivel') == '4' ) {
                            $arRecordAF[$inCountAF]['nivel_passivo'] = 4;
                            $arRecordAF[$inCountAF]['nom_conta_passivo'] = $rsRecordSet->getCampo('nom_conta');
                            $arRecordAF[$inCountAF]['saldo_atual_passivo'] = $nuValor;

                            $flTotalPsFinanceiro += $nuValor;
                            $inUltimoQuartoNivelFP = $inCountAF;
                            $inCountAF++;
                        }

                        //QUINTO NIVEL
                        if ( $rsRecordSet->getCampo('nivel') == '5' ) {
                            $arRecordAF[$inCountAF]['nivel_passivo'] = 5;
                            $arRecordAF[$inCountAF]['nom_conta_passivo'] = $rsRecordSet->getCampo('nom_conta');
                            $arRecordAF[$inCountAF]['saldo_atual_passivo'] = $nuValor;

                            $flTotalPsFinanceiro += $nuValor;
                            $flTotalPsFinanceiro -= $arRecordAF[$inUltimoQuartoNivelFP]['saldo_atual_passivo'];
                            $arRecordAF[$inUltimoQuartoNivelFP]['saldo_atual_passivo'] = '';
                            $inCountAF++;
                        }

                        // Se o Relatório for Analístico lista as contas a partir do nível 5
                        if ($this->inTipoRelatorio != 1) {
                            //SEXTO AO DECIMO NIVEL
                            if ($rsRecordSet->getCampo('nivel') > 5 && $rsRecordSet->getCampo('nivel') <= 10) {

                                $arRecordAF[$inCountAF]['nivel_passivo'] = $rsRecordSet->getCampo('nivel');;
                                $arRecordAF[$inCountAF]['nom_conta_passivo'] = $rsRecordSet->getCampo('nom_conta');
                                $arRecordAF[$inCountAF]['saldo_atual_passivo'] = $nuValor;

                                $inCountAF++;

                            }

                        }

                    }

                }

                //PASSIVO PERMANENTE
                if ( strtoupper($rsRecordSet->getCampo('nom_sistema')) == strtoupper('Patrimonial') OR $arSistemaContabil[$stCodEstrutural][strtoupper('Patrimonial')]) {

                    $nuValor = ($arSistemaContabil[$stCodEstrutural][strtoupper('Patrimonial')]) ? $arSistemaContabil[$stCodEstrutural][strtoupper('Patrimonial')] : $rsRecordSet->getCampo('vl_saldo_atual');

                    if ($boCabecalhoPsPermanente == false) {

                        $arRecordAP[$inCountAP]['nivel_passivo'] = '';
                        $arRecordAP[$inCountAP]['nom_conta_passivo'] = '';
                        $arRecordAP[$inCountAP]['saldo_atual_passivo'] = '';
                        $inCountAP++;

                        $arRecordAP[$inCountAP]['nivel_passivo'] = 2;
                        $arRecordAP[$inCountAP]['nom_conta_passivo'] = 'Passivo Permanente';
                        $arRecordAP[$inCountAP]['saldo_atual_passivo'] = '';
                        $boCabecalhoPsPermanente = true;
                        $inCountAP++;
                    }

                    //TERCEIRO NIVEL
                    if ( $rsRecordSet->getCampo('nivel') == '3' ) {
                        $arRecordAP[$inCountAP]['nivel_passivo'] = 3;
                        $arRecordAP[$inCountAP]['nom_conta_passivo'] = $rsRecordSet->getCampo('nom_conta');
                        $arRecordAP[$inCountAP]['saldo_atual_passivo'] = '';
                        $inCountAP++;
                    }
                    if ($nuValor != 0) {
                        //QUARTO NIVEL
                        if ( $rsRecordSet->getCampo('nivel') == '4' ) {
                            $arRecordAP[$inCountAP]['nivel_passivo'] = 4;
                            $arRecordAP[$inCountAP]['nom_conta_passivo'] = $rsRecordSet->getCampo('nom_conta');
                            $arRecordAP[$inCountAP]['saldo_atual_passivo'] = $nuValor;

                            $flTotalPsPermanente += $nuValor;
                            $inUltimoQuartoNivelPP = $inCountAP;
                            $inCountAP++;
                        }

                        //QUINTO NIVEL
                        if ( $rsRecordSet->getCampo('nivel') == '5' ) {
                            $arRecordAP[$inCountAP]['nivel_passivo'] = 5;
                            $arRecordAP[$inCountAP]['nom_conta_passivo'] = $rsRecordSet->getCampo('nom_conta');
                            $arRecordAP[$inCountAP]['saldo_atual_passivo'] = $nuValor;

                            $flTotalPsPermanente += $nuValor;
                            $flTotalPsPermanente -= $arRecordAP[$inUltimoQuartoNivelPP]['saldo_atual_passivo'];
                            $arRecordAP[$inUltimoQuartoNivelPP]['saldo_atual_passivo'] = '';
                            $inCountAP++;
                        }

                        // Se o Relatório for Analístico lista as contas a partir do nível 5
                        if ($this->inTipoRelatorio != 1) {
                            //SEXTO AO DECIMO NIVEL
                            if ($rsRecordSet->getCampo('nivel') > 5 && $rsRecordSet->getCampo('nivel') <= 10) {

                                $arRecordAP[$inCountAP]['nivel_passivo'] = $rsRecordSet->getCampo('nivel');
                                $arRecordAP[$inCountAP]['nom_conta_passivo'] = $rsRecordSet->getCampo('nom_conta');
                                $arRecordAP[$inCountAP]['saldo_atual_passivo'] = $nuValor;

                                $inCountAP++;

                            }

                        }

                    }

                }

            }
            $rsRecordSet->proximo();
        }

        $inCountAF = count( $arRecordAF );
        $arRecordAF[$inCountAF]['nivel'] = '';
        $arRecordAF[$inCountAF]['nom_conta'] = '';
        $arRecordAF[$inCountAF]['saldo_atual'] = '';
        $arRecordAF[$inCountAF]['nivel_passivo'] = '';
        $arRecordAF[$inCountAF]['nom_conta_passivo'] = '';
        $arRecordAF[$inCountAF]['saldo_atual_passivo'] = '';
        $inCountAF++;

        //TOTAL DO ATIVO E PASSIVO FINANCEIRO
        $arRecordAF[$inCountAF]['nivel'] = 2;
        $arRecordAF[$inCountAF]['nom_conta'] = 'Total do Ativo Financeiro';
        $arRecordAF[$inCountAF]['saldo_atual'] = $flTotalAtFinanceiro;
        $arRecordAF[$inCountAF]['nivel_passivo'] = 2;
        $arRecordAF[$inCountAF]['nom_conta_passivo'] = 'Total do Passivo Financeiro';
        $arRecordAF[$inCountAF]['saldo_atual_passivo'] = $flTotalPsFinanceiro;

        $arRecord = array_merge( $arRecord, $arRecordAF, $arRecordAP );
        $inCount = count( $arRecord );

        //TOTAL DO ATIVO E PASSIVO PERMANENTE
        $arRecord[$inCount]['nivel'] = '';
        $arRecord[$inCount]['nom_conta'] = '';
        $arRecord[$inCount]['saldo_atual'] = '';
        $arRecord[$inCount]['nivel_passivo'] = '';
        $arRecord[$inCount]['nom_conta_passivo'] = '';
        $arRecord[$inCount]['saldo_atual_passivo'] = '';
        $inCount++;

        $arRecord[$inCount]['nivel'] = 2;
        $arRecord[$inCount]['nom_conta'] = 'Total do Ativo Permanente';
        $arRecord[$inCount]['saldo_atual'] = $flTotalAtPermanente;
        $arRecord[$inCount]['nivel_passivo'] = 2;
        $arRecord[$inCount]['nom_conta_passivo'] = 'Total do Passivo Permanente';
        $arRecord[$inCount]['saldo_atual_passivo'] = $flTotalPsPermanente;
        $inCount++;

        //TOTAL DO ATIVO E PASSIVO REAL
        $arRecord[$inCount]['nivel'] = '';
        $arRecord[$inCount]['nom_conta'] = '';
        $arRecord[$inCount]['saldo_atual'] = '';
        $arRecord[$inCount]['nivel_passivo'] = '';
        $arRecord[$inCount]['nom_conta_passivo'] = '';
        $arRecord[$inCount]['saldo_atual_passivo'] = '';
        $inCount++;

        $arRecord[$inCount]['nivel'] = 2;
        $arRecord[$inCount]['nom_conta'] = 'Ativo Real';
        $flTotalAtivoReal = $flTotalAtFinanceiro + $flTotalAtPermanente;
        $arRecord[$inCount]['saldo_atual'] = $flTotalAtivoReal;
        $arRecord[$inCount]['nivel_passivo'] = 2;
        $arRecord[$inCount]['nom_conta_passivo'] = 'Passivo Real';
        $flTotalPassivoReal = $flTotalPsFinanceiro + $flTotalPsPermanente;
        $arRecord[$inCount]['saldo_atual_passivo'] = $flTotalPassivoReal;
        $inCount++;

        $arRecord[$inCount]['nivel'] = '';
        $arRecord[$inCount]['nom_conta'] = '';
        $arRecord[$inCount]['saldo_atual'] = '';
        $inCount++;

        $rsRecordSet->setPrimeiroElemento();
        while ( !$rsRecordSet->eof() ) {

            $arCodEstrutural = explode( "." , $rsRecordSet->getCampo('cod_estrutural') );
            $stCodEstrutural = $rsRecordSet->getCampo('cod_estrutural');

            //PASSIVO - SALDO PATRIMONIAL
            if ($arCodEstrutural[0].'.'.$arCodEstrutural[1] == '2.4') {

                if ( strtoupper($rsRecordSet->getCampo('nom_sistema')) == strtoupper('Patrimonial') OR $arSistemaContabil[$stCodEstrutural][strtoupper('Patrimonial')]) {

                    if ($boCabecalhoSaldoPatrimonial == false) {

                        $arRecord[$inCount]['nivel_passivo'] = 2;
                        $arRecord[$inCount]['nom_conta_passivo'] = 'Saldo Patrimonial';
                        $arRecord[$inCount]['saldo_atual_passivo'] = '';
                        $boCabecalhoSaldoPatrimonial = true;
                        $inCount++;
                    }

                    //TERCEIRO NIVEL
                    if ( $rsRecordSet->getCampo('nivel') == '3' ) {
                        $arRecord[$inCount]['nivel_passivo'] = 3;
                        $arRecord[$inCount]['nom_conta_passivo'] = $rsRecordSet->getCampo('nom_conta');
                        $arRecord[$inCount]['saldo_atual_passivo'] = '';
                        $inCount++;
                    }

                    if ( $rsRecordSet->getCampo('vl_saldo_atual') != 0 ) {
                        //QUARTO NIVEL
                        if ( $rsRecordSet->getCampo('nivel') == '4' ) {
                            $arRecord[$inCount]['nivel_passivo'] = 4;
                            $arRecord[$inCount]['nom_conta_passivo'] = $rsRecordSet->getCampo('nom_conta');
                            $arRecord[$inCount]['saldo_atual_passivo'] = $rsRecordSet->getCampo('vl_saldo_atual');

                            $flTotalSaldoPatrimonial += $rsRecordSet->getCampo('vl_saldo_atual');
                            $inUltimoQuartoNivelSP = $inCount;
                            $inCount++;
                        }

                        //QUINTO NIVEL
                        if ( $rsRecordSet->getCampo('nivel') == '5' ) {
                            $arRecord[$inCount]['nivel_passivo'] = 5;
                            $arRecord[$inCount]['nom_conta_passivo'] = $rsRecordSet->getCampo('nom_conta');
                            $arRecord[$inCount]['saldo_atual_passivo'] = $rsRecordSet->getCampo('vl_saldo_atual');

                            $flTotalSaldoPatrimonial += $rsRecordSet->getCampo('vl_saldo_atual');
                            $flTotalSaldoPatrimonial -= $arRecord[$inUltimoQuartoNivelSP]['saldo_atual_passivo'];
                            $arRecord[$inUltimoQuartoNivelSP]['saldo_atual_passivo'] = '';
                            $inCount++;
                        }

                        // Se o Relatório for Analístico lista as contas a partir do nível 5
                        if ($this->inTipoRelatorio != 1) {
                            //SEXTO AO DECIMO NIVEL
                            if ($rsRecordSet->getCampo('nivel') > 5 && $rsRecordSet->getCampo('nivel') <= 10) {

                                $arRecord[$inCount]['nivel_passivo'] = $rsRecordSet->getCampo('nivel');
                                $arRecord[$inCount]['nom_conta_passivo'] = $rsRecordSet->getCampo('nom_conta');
                                $arRecord[$inCount]['saldo_atual_passivo'] = $rsRecordSet->getCampo('vl_saldo_atual');
                                $inCount++;
                            }

                        }

                    }
                }
            }
            $rsRecordSet->proximo();
        }

        //VERIFICA SE HÁ SUPERÁVIT ACUMULADO OU DÉFICIT ACUMULADO NO SALDO PATRIMONIAL
        if ( $flTotalAtivoReal > (( $flTotalPassivoReal + $flTotalSaldoPatrimonial ) * (-1)) ) {
            $flSuperavit = $flTotalAtivoReal + ( $flTotalPassivoReal + $flTotalSaldoPatrimonial );
            if ($flSuperavit > 0) {
                $flSuperavit = $flSuperavit * (-1);
            }
            $stNomConta   = 'Superávit Acumulado';
            $flValorConta = $flSuperavit;
        } else {
            $stNomConta   = 'Déficit Acumulado';
            $flDeficit = (($flTotalPassivoReal + $flTotalSaldoPatrimonial) * (-1)) - $flTotalAtivoReal;
            if ($flDeficit < 0) {
                $flDeficit = $flDeficit * (-1);
            }
            $flValorConta = $flDeficit;

        }

        if ( substr( $this->stDtFinal, 3, 2 ) != 12 ) {
            $arRecord[$inCount]['nivel_passivo'] = 4;
            $arRecord[$inCount]['nom_conta_passivo'] = $stNomConta;
            $arRecord[$inCount]['saldo_atual_passivo'] = $flValorConta;
            $flTotalSaldoPatrimonial += $flValorConta;
            $inCount++;
        }

        //TOTAL DO SALDO PATRIMONIAL
        $arRecord[$inCount]['nivel_passivo'] = '';
        $arRecord[$inCount]['nom_conta_passivo'] = '';
        $arRecord[$inCount]['saldo_atual_passivo'] = '';
        $inCount++;

        $arRecord[$inCount]['nivel_passivo'] = 2;
        $arRecord[$inCount]['nom_conta_passivo'] = 'Total do Saldo Patrimonial';
        $arRecord[$inCount]['saldo_atual_passivo'] = $flTotalSaldoPatrimonial;
        $inCount++;

        $inCountAux = $inCount;

        $rsRecordSet->setPrimeiroElemento();
        while ( !$rsRecordSet->eof() ) {

            $arCodEstrutural = explode( "." , $rsRecordSet->getCampo('cod_estrutural') );
            $stCodEstrutural = $rsRecordSet->getCampo('cod_estrutural');

            //ATIVO
            if ($arCodEstrutural[0] == '1') {

                //ATIVO COMPENSADO
                if( strtoupper($rsRecordSet->getCampo('nom_sistema')) == strtoupper('Compensado') OR
                    strtoupper($rsRecordSet->getCampo('nom_sistema')) == strtoupper('Orçamentário') OR
                    $arSistemaContabil[$stCodEstrutural][strtoupper('Compensado')] OR
                    $arSistemaContabil[$stCodEstrutural][strtoupper('Orçamentário')]
                ) {

                    if ($boCabecalhoAtCompensado == false) {

                        $arRecord[$inCount]['nivel_passivo'] = '';
                        $arRecord[$inCount]['nom_conta_passivo'] = '';
                        $arRecord[$inCount]['saldo_atual_passivo'] = '';
                        $inCount++;

                        $arRecord[$inCount]['nivel'] = 2;
                        $arRecord[$inCount]['nom_conta'] = 'Ativo Compensado';
                        $arRecord[$inCount]['saldo_atual'] = '';
                        $boCabecalhoAtCompensado = true;
                        $inCount++;
                    }

                    if ( $rsRecordSet->getCampo('vl_saldo_atual') != 0 ) {
                        //TERCEIRO NIVEL
                        if ( $rsRecordSet->getCampo('nivel') == '3' ) {
                            $arRecord[$inCount]['nivel'] = 3;
                            $arRecord[$inCount]['nom_conta'] = $rsRecordSet->getCampo('nom_conta');
                            $arRecord[$inCount]['saldo_atual'] = $rsRecordSet->getCampo('vl_saldo_atual');
                            $flTotalAtCompensado += $rsRecordSet->getCampo('vl_saldo_atual');
                            $inCount++;
                        }

                        // Se o Relatório for Analístico lista as contas a partir do nível 3
                        if ($this->inTipoRelatorio != 1) {
                            //QUARTO AO DECIMO NIVEL
                            if ($rsRecordSet->getCampo('nivel') > 3 && $rsRecordSet->getCampo('nivel') <= 10) {

                                $arRecord[$inCount]['nivel'] = $rsRecordSet->getCampo('nivel');
                                $arRecord[$inCount]['nom_conta'] = $rsRecordSet->getCampo('nom_conta');
                                $arRecord[$inCount]['saldo_atual'] = $rsRecordSet->getCampo('vl_saldo_atual');

                                $inCount++;
                            }

                        }

                    }
                }
            }
            $rsRecordSet->proximo();
        }

        $inCount = $inCountAux;

        //PASSIVO COMPENSADO
        $rsRecordSet->setPrimeiroElemento();
        while ( !$rsRecordSet->eof() ) {

            $arCodEstrutural = explode( "." , $rsRecordSet->getCampo('cod_estrutural') );
            $stCodEstrutural = $rsRecordSet->getCampo('cod_estrutural');

            //PASSIVO
            if ($arCodEstrutural[0] == '2') {

                //PASSIVO COMPENSADO
                if( strtoupper($rsRecordSet->getCampo('nom_sistema')) == strtoupper('Compensado') OR
                    strtoupper($rsRecordSet->getCampo('nom_sistema')) == strtoupper('Orçamentário') OR
                    $arSistemaContabil[$stCodEstrutural][strtoupper('Compensado')] OR
                    $arSistemaContabil[$stCodEstrutural][strtoupper('Orçamentário')]
                ) {

                    if ($boCabecalhoPsCompensado == false) {

                        $arRecord[$inCount]['nivel_passivo'] = '';
                        $arRecord[$inCount]['nom_conta_passivo'] = '';
                        $arRecord[$inCount]['saldo_atual_passivo'] = '';
                        $inCount++;

                        $arRecord[$inCount]['nivel_passivo'] = 2;
                        $arRecord[$inCount]['nom_conta_passivo'] = 'Passivo Compensado';
                        $arRecord[$inCount]['saldo_atual_passivo'] = '';
                        $boCabecalhoPsCompensado = true;
                        $inCount++;
                    }
                    if ( $rsRecordSet->getCampo('vl_saldo_atual') != 0 ) {
                        //TERCEIRO NIVEL
                        if ( $rsRecordSet->getCampo('nivel') == '3' ) {
                            $arRecord[$inCount]['nivel_passivo'] = 3;
                            $arRecord[$inCount]['nom_conta_passivo'] = $rsRecordSet->getCampo('nom_conta');
                            $arRecord[$inCount]['saldo_atual_passivo'] = $rsRecordSet->getCampo('vl_saldo_atual');
                            $flTotalPsCompensado += $rsRecordSet->getCampo('vl_saldo_atual');
                            $inCount++;
                        }

                        // Se o Relatório for Analístico lista as contas a partir do nível 3
                        if ($this->inTipoRelatorio != 1) {
                            //QUARTO AO DECIMO NIVEL
                            if ($rsRecordSet->getCampo('nivel') > 3 && $rsRecordSet->getCampo('nivel') <= 10) {

                                $arRecord[$inCount]['nivel_passivo'] = $rsRecordSet->getCampo('nivel');
                                $arRecord[$inCount]['nom_conta_passivo'] = $rsRecordSet->getCampo('nom_conta');
                                $arRecord[$inCount]['saldo_atual_passivo'] = $rsRecordSet->getCampo('vl_saldo_atual');
                                $inCount++;
                            }

                        }

                    }

                }
            }
            $rsRecordSet->proximo();
        }

        $inCount = count( $arRecord );

        //TOTAL DO ATIVO E PASSIVO COMPENSADO
        $arRecord[$inCount]['nivel'] = '';
        $arRecord[$inCount]['nom_conta'] = '';
        $arRecord[$inCount]['saldo_atual'] = '';
        $arRecord[$inCount]['nivel_passivo'] = '';
        $arRecord[$inCount]['nom_conta_passivo'] = '';
        $arRecord[$inCount]['saldo_atual_passivo'] = '';
        $inCount++;

        $arRecord[$inCount]['nivel'] = 2;
        $arRecord[$inCount]['nom_conta'] = 'Total do Ativo Compensado';
        $arRecord[$inCount]['saldo_atual'] = $flTotalAtCompensado;
        $arRecord[$inCount]['nivel_passivo'] = 2;
        $arRecord[$inCount]['nom_conta_passivo'] = 'Total do Passivo Compensado';
        $arRecord[$inCount]['saldo_atual_passivo'] = $flTotalPsCompensado;
        $inCount++;

        $inCount = count( $arRecord );

        $arRecord[$inCount]['nivel'] = '';
        $arRecord[$inCount]['nom_conta'] = '';
        $arRecord[$inCount]['saldo_atual'] = '';
        $arRecord[$inCount]['nivel_passivo'] = '';
        $arRecord[$inCount]['nom_conta_passivo'] = '';
        $arRecord[$inCount]['saldo_atual_passivo'] = '';
        $inCount++;

        $arRecord[$inCount]['nivel'] = 2;
        $arRecord[$inCount]['nom_conta'] = 'Total do Ativo';
        $arRecord[$inCount]['saldo_atual'] = $flTotalAtivoReal + $flTotalAtCompensado;
        $arRecord[$inCount]['nivel_passivo'] = 2;
        $arRecord[$inCount]['nom_conta_passivo'] = 'Total do Passivo';
        $arRecord[$inCount]['saldo_atual_passivo'] = $flTotalPassivoReal + $flTotalSaldoPatrimonial + $flTotalPsCompensado;

        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche( $arRecord );

        $rsRecordSet->addFormatacao( "saldo_atual"         , "CONTABIL" );
        $rsRecordSet->addFormatacao( "saldo_atual_passivo" , "CONTABIL" );

        return $obErro;
    }
}
