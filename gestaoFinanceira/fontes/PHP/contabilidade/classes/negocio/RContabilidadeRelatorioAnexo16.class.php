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

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Jõao Rafael Tissot

    * @ignore

    * $Id: RContabilidadeRelatorioAnexo16.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO );
include_once( CAM_GF_CONT_MAPEAMENTO."FContabilidadeDemoDividaFundada.class.php"             );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );

class RContabilidadeRelatorioAnexo16 extends PersistenteRelatorio
{

    /**
    * @var Object
    * @access Private
*/
    public $obFContabilidadeDemoDividaFundada;
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

    public $recordSetEntidades;

    /**
     * @access Public
     * @param String $valor
*/

    public function setRecordSetEntidades($valor) {$this->stEntidades;}
    /**
     * @access Public
     * @param String $valor
*/
    public function getRecordSetEntidades() {return $this->stEntidades;}
    /**
     * @access Public
     * @param Object $valor
*/
    public function setFContabilidadeDemoDividaFundada($valor) { $this->obFContabilidadeDemoDividaFundada  = $valor; }
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
     * @param Object $valor
*/
    public function getFContabilidadeDemoDividaFundada() { return $this->obFContabilidadeDemoDividaFundada;}
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
    * Método Construtor
    * @access Private
*/

    public function RContabilidadeRelatorioAnexo16()
    {
        $this->obFContabilidadeDemoDividaFundada = new FContabilidadeDemoDividaFundada;

    }

    /**
    * Método abstrato
    * @access Public
*/
    public function geraRecordSet(&$rsRecordSet, $stOrder = "", $stEntidades = "")
    {
        $arRecordSet = array();

        $this->obFContabilidadeDemoDividaFundada->setDado ("exercicio",  $this->inExercicio);
        $this->obFContabilidadeDemoDividaFundada->setDado ("stFiltro",   $this->stFiltro);
        $this->obFContabilidadeDemoDividaFundada->setDado ("stDtInicial",$this->stDtInicial);
        $this->obFContabilidadeDemoDividaFundada->setDado ("stDtFinal",  $this->stDtFinal);
        $this->obFContabilidadeDemoDividaFundada->setDado ("chEstilo",   $this->stEstilo);

        $obErro = $this->obFContabilidadeDemoDividaFundada->recuperaTodos( $rsRecordSet, "", "" );
        $inCount = 0;

        while ( !$rsRecordSet->eof() ) {

            $arRecordSet[$inCount]  ["cod_estrutural"]      = $rsRecordSet->getCampo("cod_estrutural");
            $arRecordSet[$inCount]  ["nivel"]               = $rsRecordSet->getCampo("nivel");
            $arRecordSet[$inCount]  ["nom_conta"]           = $rsRecordSet->getCampo("nom_conta");
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = $rsRecordSet->getCampo("vl_saldo_anterior","CONTABIL");
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = $rsRecordSet->getCampo("vl_saldo_debitos");
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = $rsRecordSet->getCampo("vl_saldo_creditos");
            $arRecordSet[$inCount++]["vl_saldo_atual"]      = $rsRecordSet->getCampo("vl_saldo_atual");
            if ( $rsRecordSet->getCampo("nivel") == 1 ) {
                $nuSaldoAnterior                           = $nuSaldoAnterior + $rsRecordSet->getCampo("vl_saldo_anterior");
                $nuSaldoDebitos                            = $nuSaldoDebitos + $rsRecordSet->getCampo("vl_saldo_debitos");
                $nuSaldoCreditos                           = $nuSaldoCreditos + $rsRecordSet->getCampo("vl_saldo_creditos");
                $nuSaldoAtual                              = $nuSaldoAtual + $rsRecordSet->getCampo("vl_saldo_atual");
            }
            $rsRecordSet->proximo();
        }
        if ( is_array($arRecordSet) ) {
            //Espaço
            $arRecordSet[$inCount]  ["cod_estrutural"]      = '';
            $arRecordSet[$inCount]  ["nivel"]               = '';
            $arRecordSet[$inCount]  ["nom_conta"]           = '';
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
            $arRecordSet[++$inCount]["vl_saldo_atual"]      = '';

            //Total Geral
            $arRecordSet[$inCount]  ["cod_estrutural"]      = '';
            $arRecordSet[$inCount]  ["nivel"]               = '';
            $arRecordSet[$inCount]  ["nom_conta"]           = 'TOTAL GERAL';
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = $nuSaldoAnterior;
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = $nuSaldoDebitos;
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = $nuSaldoCreditos;
            $arRecordSet[$inCount]  ["vl_saldo_atual"]      = $nuSaldoAtual;

            $obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;

            $inCount++;
            $arRecordSet[$inCount]  ["nom_conta"]           = 'ENTIDADES RELACIONADAS';
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_atual"]      = '';

            $arFiltro = Sessao::read('filtroRelatorio');
            foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
                $stOrdem = " cod_entidade=".$valor." ORDER BY cod_entidade";
                $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );
                $inCount++;

                $arRecordSet[$inCount]  ["nom_conta"]			= $rsEntidades->getCampo('nom_cgm');
                $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
                $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
                $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
                $arRecordSet[$inCount]  ["vl_saldo_atual"]      = '';
            }

            $inCount++;
            $arRecordSet[$inCount]  ["nom_conta"]			= 'EXERCÍCIO        '.$this->inExercicio;
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_atual"]      = '';

            $inCount++;
            $arRecordSet[$inCount]  ["nom_conta"]			= '';
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_atual"]      = '';

            $inCount++;
            $arRecordSet[$inCount]  ["nom_conta"]			= '';
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_atual"]      = '';

            $inCount++;
            $arRecordSet[$inCount]  ["nom_conta"]			= '';
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_atual"]      = '';

            $inCount++;
            $arRecordSet[$inCount]  ["nom_conta"]			= '____________________________________________';
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '____________________________________________';
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_atual"]      = '';

            $inCount++;
            $arRecordSet[$inCount]  ["nom_conta"]			= 'Assinatura do Representante';
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = 'Assinatura do Contador Responsável';
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_atual"]      = '';

        }

        $rsRecordSetNovo = new RecordSet;
        $rsRecordSetNovo->preenche( $arRecordSet );
        $rsRecordSet = $rsRecordSetNovo;

        $rsRecordSet->addFormatacao("vl_saldo_anterior","CONTABIL");
        $rsRecordSet->addFormatacao("vl_saldo_debitos", "CONTABIL");
        $rsRecordSet->addFormatacao("vl_saldo_creditos","CONTABIL");
        $rsRecordSet->addFormatacao("vl_saldo_atual",   "CONTABIL");

        return $obErro;
    }
}
