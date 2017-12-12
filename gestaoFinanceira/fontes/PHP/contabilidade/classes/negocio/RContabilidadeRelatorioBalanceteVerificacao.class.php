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
    * Classe de regra de relatório para o balancete de verificação
    * Data de Criação   : 29/11/2004

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    * $Id: RContabilidadeRelatorioBalanceteVerificacao.class.php 64153 2015-12-09 19:16:02Z evandro $

    * Casos de uso: uc-02.02.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO                           );
include_once( CAM_GF_CONT_MAPEAMENTO."FContabilidadeBalanceteVerificacao.class.php"             );

class RContabilidadeRelatorioBalanceteVerificacao extends PersistenteRelatorio
{

    /**
        * @var Object
        * @access Private
    */
    public $obFContabilidadeBalanceteVerificacao;
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
        * @var Integer
        * @access Private
    */
    public $inCodSistema;

    /**
         * @access Public
         * @param Object $valor
    */
    public function setFContabilidadeBalanceteVerificacao($valor) { $this->obFContabilidadeBalanceteVerificacao  = $valor; }
    /**
         * @access Public
         * @param String $valor
    */
    public function setFiltro($valor) { $this->stFiltro                              = $valor; }
    /**
         * @access Public
         * @param String $valor
    */
    public function setDtInicial($valor) { $this->stDtInicial                           = $valor; }
    /**
         * @access Public
         * @param String $valor
    */
    public function setDtFinal($valor) { $this->stDtFinal                             = $valor; }
    /**
         * @access Public
         * @param String $valor
    */
    public function setEstilo($valor) { $this->stEstilo                              = $valor; }
    /**
         * @access Public
         * @param Integer $valor
    */
    public function setExercicio($valor) { $this->inExercicio                           = $valor; }
    /**
         * @access Public
         * @param Integer $valor
    */
    public function setCodSistema($valor) { $this->inCodSistema                          = $valor; }

    /**
         * @access Public
         * @param Object $valor
    */
    public function getFContabilidadeBalanceteVerificacao() { return $this->obFContabilidadeBalanceteVerificacao; }
    /**
         * @access Public
         * @param String $valor
    */
    public function getFiltro() { return $this->stFiltro                    ; }
    /**
         * @access Public
         * @param String $valor
    */
    public function getDtInicial() { return $this->stDtInicial                 ; }
    /**
         * @access Public
         * @param String $valor
    */
    public function getDtFinal() { return $this->stDtFinal                   ; }
    /**
         * @access Public
         * @param String $valor
    */
    public function getEstilo() { return $this->stEstilo                    ; }
    /**
         * @access Public
         * @param Integer $valor
    */
    public function getExercicio() { return $this->inExercicio                 ; }
    /**
         * @access Public
         * @param Integer $valor
    */
    public function getCodSistema() { return $this->inCodSistema                ; }

    /**
        * Método Construtor
        * @access Private
    */
    public function RContabilidadeRelatorioBalanceteVerificacao()
    {
        $this->obFContabilidadeBalanceteVerificacao = new FContabilidadeBalanceteVerificacao;
    }

    /**
        * Método abstrato
        * @access Public
    */
    public function geraRecordSet(&$rsRecordSet, $stOrder = '')
    {
       $arRecordSet = array();

       $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
       $obTAdministracaoConfiguracao->setDado('cod_modulo', 9);
       $obTAdministracaoConfiguracao->setDado('exercicio', Sessao::getExercicio());
       $obTAdministracaoConfiguracao->setDado('parametro', 'masc_plano_contas');
       $obTAdministracaoConfiguracao->pegaConfiguracao($stMascara, '');

       $this->obFContabilidadeBalanceteVerificacao->setDado('exercicio'    , $this->inExercicio);
       $this->obFContabilidadeBalanceteVerificacao->setDado('stFiltro'     , $this->stFiltro);
       $this->obFContabilidadeBalanceteVerificacao->setDado('stDtInicial'  , $this->stDtInicial);
       $this->obFContabilidadeBalanceteVerificacao->setDado('stDtFinal'    , $this->stDtFinal);
       $this->obFContabilidadeBalanceteVerificacao->setDado('chEstilo'     , $this->stEstilo);
       $this->obFContabilidadeBalanceteVerificacao->setDado('inCodEntidade', $this->inCodEntidade);

       $obErro = $this->obFContabilidadeBalanceteVerificacao->recuperaTodos($rsRecordSet, '', '');

       $inCount = 0;
       while (!$rsRecordSet->eof()) {
            if ( Sessao::getExercicio() > '2012' ) {
                switch ($rsRecordSet->getCampo('cod_sistema')) {
                case 1:
                    $stCodSistema = 'P';
                    break;
                case 2:
                    $stCodSistema = 'O';
                    break;
                case 3:
                    $stCodSistema = 'C';
                    break;
                default:
                    $stCodSistema = '';
                    break;
                }
            } else {
                switch ($rsRecordSet->getCampo('cod_sistema')) {
                case 1:
                    $stCodSistema = 'F';
                    break;
                case 2:
                    $stCodSistema = 'P';
                    break;
                case 3:
                    $stCodSistema = 'O';
                    break;
                case 4:
                    $stCodSistema = 'C';
                    break;
                default:
                    $stCodSistema = '';
                    break;
                }
            }

            switch (trim($rsRecordSet->getCampo('indicador_superavit'))) {
                case "financeiro":
                    $stSuperavit = 'F';
                    break;
                case "permanente":
                    $stSuperavit = 'P';
                    break;
                case "misto":
                    $stSuperavit = 'M';
                    break;
                default:
                    $stSuperavit = '';
                    break;

            }

            $arRecordSet[$inCount]  ['cod_estrutural']            = SistemaLegado::doMask($rsRecordSet->getCampo('cod_estrutural'), $stMascara);
            $arRecordSet[$inCount]  ['nivel']                     = $rsRecordSet->getCampo('nivel');
            $arRecordSet[$inCount]  ['cod_sistema']               = $stCodSistema;
            $arRecordSet[$inCount]  ['indicador_superavit']       = $stSuperavit;
            $arRecordSet[$inCount]  ['nom_conta']                 = $rsRecordSet->getCampo('nom_conta');
            $arRecordSet[$inCount]  ['vl_saldo_anterior']         = $rsRecordSet->getCampo('vl_saldo_anterior');
            $arRecordSet[$inCount]  ['vl_saldo_debitos']          = $rsRecordSet->getCampo('vl_saldo_debitos');
            $arRecordSet[$inCount]  ['vl_saldo_creditos']         = $rsRecordSet->getCampo('vl_saldo_creditos');
            $arRecordSet[$inCount++]['vl_saldo_atual']            = $rsRecordSet->getCampo('vl_saldo_atual');

            if ($rsRecordSet->getCampo('nivel') == 1) {
                $nuSaldoAnterior = bcadd($rsRecordSet->getCampo('vl_saldo_anterior'), $nuSaldoAnterior, 2);
                $nuSaldoDebitos  = bcadd($rsRecordSet->getCampo('vl_saldo_debitos') , $nuSaldoDebitos , 2);
                $nuSaldoCreditos = bcadd($rsRecordSet->getCampo('vl_saldo_creditos'), $nuSaldoCreditos, 2);
                $nuSaldoAtual    = bcadd($rsRecordSet->getCampo('vl_saldo_atual')   , $nuSaldoAtual   , 2);

            }
            $rsRecordSet->proximo();
        }

        if (is_array($arRecordSet)) {
            //Espaço
            $arRecordSet[$inCount]  ['cod_estrutural']      = '';
            $arRecordSet[$inCount]  ['nivel']               = '';
            $arRecordSet[$inCount]  ['nom_conta']           = '';
            $arRecordSet[$inCount]  ['vl_saldo_anterior']   = '';
            $arRecordSet[$inCount]  ['vl_saldo_debitos']    = '';
            $arRecordSet[$inCount]  ['vl_saldo_creditos']   = '';
            $arRecordSet[++$inCount]['vl_saldo_atual']      = '';

            $nuSaldoAnterior = str_pad($nuSaldoAnterior, 4 , '.00');
            $nuSaldoDebitos  = str_pad($nuSaldoDebitos , 4 , '.00');
            $nuSaldoCreditos = str_pad($nuSaldoCreditos, 4 , '.00');
            $nuSaldoAtual    = str_pad($nuSaldoAtual   , 4 , '.00');

            //Total Geral
            $arRecordSet[$inCount]  ['cod_estrutural']          = '';
            $arRecordSet[$inCount]  ['nivel']                   = '';
            $arRecordSet[$inCount]  ['indicador_superavit']     = '';
            $arRecordSet[$inCount]  ['nom_conta']               = 'T O T A L  G E R A L .......';
            $arRecordSet[$inCount]  ['vl_saldo_anterior']       = $nuSaldoAnterior;
            $arRecordSet[$inCount]  ['vl_saldo_debitos']        = $nuSaldoDebitos;
            $arRecordSet[$inCount]  ['vl_saldo_creditos']       = $nuSaldoCreditos;
            $arRecordSet[$inCount]  ['vl_saldo_atual']          = $nuSaldoAtual;
        }

        $rsRecordSetNovo = new RecordSet;
        $rsRecordSetNovo->preenche($arRecordSet);
        $rsRecordSet = $rsRecordSetNovo;

        $rsRecordSet->addFormatacao('vl_saldo_anterior', 'CONTABIL');
        $rsRecordSet->addFormatacao('vl_saldo_debitos' , 'CONTABIL');
        $rsRecordSet->addFormatacao('vl_saldo_creditos', 'CONTABIL');
        $rsRecordSet->addFormatacao('vl_saldo_atual'   , 'CONTABIL');

        return $obErro;
    }
}
