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
    * Classe de regra de relatório para o Demonstrativo da Dívida Flutuante
    * Data de Criação   : 24/05/2005

    * @author Desenvolvedor: Cleisson Barbosa da Silva
    * @author Analista: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Regra

    * $Id: RContabilidadeRelatorioAnexo17.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO                           );
include_once( CAM_GF_CONT_MAPEAMENTO."FContabilidadeDemoDividaFlutuante.class.php"             );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );

class RContabilidadeRelatorioAnexo17 extends PersistenteRelatorio
{
    /**
    * @var Object
    * @access Private
*/
    public $obFContabilidadeDemoDividaFlutuante;
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
    public $inTipoRelatorio;

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
    public function setFContabilidadeDemoDividaFlutuante($valor) { $this->obFContabilidadeDemoDividaFlutuante  = $valor; }
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
    public function setTipoRelatorio($valor) { $this->inTipoRelatorio                       = $valor; }

    /**
     * @access Public
     * @param Object $valor
*/
    public function getFContabilidadeDemoDividaFlutuante() { return $this->obFContabilidadeDemoDividaFlutuante;}
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
    public function getTipoRelatorio() { return $this->inTipoRelatorio             ; }
/**
    * Método Construtor
    * @access Private
*/

    public function RContabilidadeRelatorioAnexo17()
    {
        $this->obFContabilidadeDemoDividaFlutuante = new FContabilidadeDemoDividaFlutuante;

    }

    /**
    * Método abstrato
    * @access Public
*/
    public function geraRecordSet(&$rsRecordSet, $stOrder = "", $stEntidades = "")
    {

        $arRecordSet = array();

        $this->obFContabilidadeDemoDividaFlutuante->setDado ("exercicio",       $this->inExercicio);
        $this->obFContabilidadeDemoDividaFlutuante->setDado ("stFiltro",        $this->stFiltro);
        $this->obFContabilidadeDemoDividaFlutuante->setDado ("stDtInicial",     $this->stDtInicial);
        $this->obFContabilidadeDemoDividaFlutuante->setDado ("stDtFinal",       $this->stDtFinal);
        $this->obFContabilidadeDemoDividaFlutuante->setDado ("chEstilo",        $this->stEstilo);
        $this->obFContabilidadeDemoDividaFlutuante->setDado ("inTipoRelatorio", $this->inTipoRelatorio );

        $obErro = $this->obFContabilidadeDemoDividaFlutuante->recuperaTodos( $rsRecordSet, "", "" );
        $inCount = 0;

//		$rsRecordSet->addFormatacao("vl_saldo_anterior", "CONTABIL");

        while ( !$rsRecordSet->eof() ) {

            $arRecordSet[$inCount]["cod_estrutural"]      = $rsRecordSet->getCampo("cod_estrutural");
            $arRecordSet[$inCount]["nivel"]               = $rsRecordSet->getCampo("nivel");
            $arRecordSet[$inCount]["nom_conta"]           = $rsRecordSet->getCampo("nom_conta");
            $arRecordSet[$inCount]["vl_saldo_anterior"]   = $rsRecordSet->getCampo("vl_saldo_anterior","CONTABIL");
            $arRecordSet[$inCount]["vl_saldo_debitos"]    = $rsRecordSet->getCampo("vl_saldo_debitos");
            $arRecordSet[$inCount]["vl_saldo_creditos"]   = $rsRecordSet->getCampo("vl_saldo_creditos");
            $arRecordSet[$inCount]["vl_saldo_atual"]      = $rsRecordSet->getCampo("vl_saldo_atual");
            $inCount++;
            if ( $this->getTipoRelatorio() == 1 ) { // Sintético
                if ( $rsRecordSet->getCampo("nivel") >= 5 ) {
                    $nuSaldoAnterior                           = $nuSaldoAnterior + $rsRecordSet->getCampo("vl_saldo_anterior");
                    $nuSaldoDebitos                            = $nuSaldoDebitos + $rsRecordSet->getCampo("vl_saldo_debitos");
                    $nuSaldoCreditos                           = $nuSaldoCreditos + $rsRecordSet->getCampo("vl_saldo_creditos");
                    $nuSaldoAtual                              = $nuSaldoAtual + $rsRecordSet->getCampo("vl_saldo_atual");
                }
            }
            $rsRecordSet->proximo();
        }
            if ( $this->getTipoRelatorio() == 2 ) { // Analítico

                    $nuSaldoAnterior = $arRecordSet[0]["vl_saldo_anterior"];
                    $nuSaldoDebitos  = $arRecordSet[0]["vl_saldo_debitos"];
                    $nuSaldoCreditos = $arRecordSet[0]["vl_saldo_creditos"];
                    $nuSaldoAtual    = $arRecordSet[0]["vl_saldo_atual"];

            }

        if ( is_array($arRecordSet) ) {
            //Espaço
            $arRecordSet[$inCount]["cod_estrutural"]      = '';
            $arRecordSet[$inCount]["nivel"]               = '';
            $arRecordSet[$inCount]["nom_conta"]           = '';
            $arRecordSet[$inCount]["vl_saldo_anterior"]   = '';
            $arRecordSet[$inCount]["vl_saldo_debitos"]    = '';
            $arRecordSet[$inCount]["vl_saldo_creditos"]   = '';
            $arRecordSet[$inCount]["vl_saldo_atual"]      = '';
            $inCount++;

            //Total Geral
            $arRecordSet[$inCount]["cod_estrutural"]      = '';
            $arRecordSet[$inCount]["nivel"]               = '';
            $arRecordSet[$inCount]["nom_conta"]           = 'TOTAL GERAL';
            $arRecordSet[$inCount]["vl_saldo_anterior"]   = $nuSaldoAnterior;
            $arRecordSet[$inCount]["vl_saldo_debitos"]    = $nuSaldoDebitos;
            $arRecordSet[$inCount]["vl_saldo_creditos"]   = $nuSaldoCreditos;
            $arRecordSet[$inCount]["vl_saldo_atual"]      = $nuSaldoAtual;
            $inCount++;

            //Espaço
            $arRecordSet[$inCount]  ["cod_estrutural"]      = '';
            $arRecordSet[$inCount]  ["nivel"]               = '';
            $arRecordSet[$inCount]  ["nom_conta"]           = '';
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
            $arRecordSet[$inCount]["vl_saldo_atual"]      = '';

            $obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;

            $inCount++;
            $arRecordSet[$inCount]  ["nom_conta"]			= 'ENTIDADES RELACIONADAS';
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_atual"]      = '';

            $arFiltro = Sessao::read('filtroRelatorio');
            foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
                $stOrdem = " cod_entidade=".$valor." ORDER BY cod_entidade";
                $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );
                $inCount++;

                $arRecordSet[$inCount]  ["nom_conta"]           = $rsEntidades->getCampo('nom_cgm');
                $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
                $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
                $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
                $arRecordSet[$inCount]  ["vl_saldo_atual"]      = '';
            }

            $inCount++;
            $arRecordSet[$inCount]  ["nom_conta"]			= '';
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_atual"]      = '';

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
            if ($inCount % 27 == 0) {
                 $arRecordSet[$inCount]  ["nom_conta"]			= '';
                 $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
                 $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
                 $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
                 $arRecordSet[$inCount]  ["vl_saldo_atual"]      = '';
                 $inCount++;
            }

             $arRecordSet[$inCount]  ["nom_conta"]			= '                                    ____________________________________________';
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '____________________________________________';
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_atual"]      = '';

            $inCount++;
            $arRecordSet[$inCount]  ["nom_conta"]			= '                                                        Assinatura do Representante';
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = 'Assinatura do Contador Responsável             ';
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_atual"]      = '';

        }

        $rsRecordSetNovo = new RecordSet;
        $rsRecordSetNovo->preenche( $arRecordSet );
        $rsRecordSet = $rsRecordSetNovo;

        $rsRecordSet->addFormatacao("vl_saldo_anterior", "CONTABIL");
        $rsRecordSet->addFormatacao("vl_saldo_debitos", "CONTABIL");
        $rsRecordSet->addFormatacao("vl_saldo_creditos","CONTABIL");
        $rsRecordSet->addFormatacao("vl_saldo_atual",   "CONTABIL");

        return $obErro;
    }
}
