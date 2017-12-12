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
    * Data de Criação   : 12/05/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Regra

    * $Id: RContabilidadeRelatorioDemoRecDespExtraOrcamento.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.15
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO                           );
include_once( CAM_GF_CONT_MAPEAMENTO."FContabilidadeDemoRecDespExtraOrcamento.class.php"             );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );

class RContabilidadeRelatorioDemoRecDespExtraOrcamento extends PersistenteRelatorio
{

    /**
    * @var Object
    * @access Private
*/
    public $obFContabilidadeDemoRecDespExtraOrcamento;
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
    public function setFContabilidadeDemoRecDespExtraOrcamento($valor) { $this->obFContabilidadeDemoRecDespExtraOrcamento  = $valor; }
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
    public function getFContabilidadeDemoRecDespExtraOrcamento() { return $this->obFContabilidadeDemoRecDespExtraOrcamento;}
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

    public function RContabilidadeRelatorioDemoRecDespExtraOrcamento()
    {
        $this->obFContabilidadeDemoRecDespExtraOrcamento = new FContabilidadeDemoRecDespExtraOrcamento;

    }

    /**
    * Método abstrato
    * @access Public
*/
    public function geraRecordSet(&$rsRecordSet, $stOrder = "", $stEntidades = "")
    {
        $arRecordSet = array();

        $this->obFContabilidadeDemoRecDespExtraOrcamento->setDado ("exercicio",  $this->inExercicio);
        $this->obFContabilidadeDemoRecDespExtraOrcamento->setDado ("stFiltro",   $this->stFiltro);
        $this->obFContabilidadeDemoRecDespExtraOrcamento->setDado ("stDtInicial",$this->stDtInicial);
        $this->obFContabilidadeDemoRecDespExtraOrcamento->setDado ("stDtFinal",  $this->stDtFinal);
        $this->obFContabilidadeDemoRecDespExtraOrcamento->setDado ("chEstilo",   $this->stEstilo);

        $obErro = $this->obFContabilidadeDemoRecDespExtraOrcamento->recuperaTodos( $rsRecordSet, "", "" );
        $inCount = 0;

        $cod_conta_atual    = 0;
        $cod_conta_anterior = 0;
        while ( !$rsRecordSet->eof() ) {
            $cod_conta_atual = $rsRecordSet->getCampo("cod_estrutural");
            if ( substr($cod_conta_atual,0,1)<>substr($cod_conta_anterior,0,1) and $cod_conta_anterior<>0) {
                //Espaço
                $arRecordSet[$inCount]  ["cod_estrutural"]      = '';
                $arRecordSet[$inCount]  ["nivel"]               = '';
                $arRecordSet[$inCount]  ["nom_conta"]           = '';
                $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
                $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
                $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
                $arRecordSet[++$inCount]["vl_saldo_atual"]      = '';

                //Total Geral da Receita
                $arRecordSet[$inCount]  ["cod_estrutural"]      = '';
                $arRecordSet[$inCount]  ["nivel"]               = '';
                $arRecordSet[$inCount]  ["nom_conta"]           = 'TOTAL GERAL DA RECEITA';
                $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = $nuSaldoAnterior;
                $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = $nuSaldoDebitos;
                $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = $nuSaldoCreditos;
                $arRecordSet[$inCount]  ["vl_saldo_atual"]    = $nuSaldoAtual;

                //Espaço
                $inCount++;
                $arRecordSet[$inCount]  ["cod_estrutural"]      = '';
                $arRecordSet[$inCount]  ["nivel"]               = '';
                $arRecordSet[$inCount]  ["nom_conta"]           = '';
                $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
                $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
                $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
                $arRecordSet[++$inCount]["vl_saldo_atual"]      = '';

                //Zera os totais para começar a contabilizar totais da Despesa
                $nuSaldoAnterior = 0;
                $nuSaldoDebitos  = 0;
                $nuSaldoCreditos = 0;
                $nuSaldoAtual    = 0;

                //Mostra o Título DESPESA EXTRA-ORÇAMENTÁRIA
                $arRecordSet[$inCount]  ["cod_estrutural"]      = '';
                $arRecordSet[$inCount]  ["nivel"]               = '';
                $arRecordSet[$inCount]  ["nom_conta"]           = 'DESPESA EXTRA-ORÇAMENTÁRIA';
                $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
                $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
                $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
                $arRecordSet[++$inCount]["vl_saldo_atual"]      = '';

                //Espaço
                $arRecordSet[$inCount]  ["cod_estrutural"]      = '';
                $arRecordSet[$inCount]  ["nivel"]               = '';
                $arRecordSet[$inCount]  ["nom_conta"]           = '';
                $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
                $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
                $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
                $arRecordSet[++$inCount]["vl_saldo_atual"]      = '';

            } elseif ($cod_conta_anterior==0) {
                //Mostra o Título RECEITA EXTRA-ORÇAMENTÁRIA
                $arRecordSet[$inCount]  ["cod_estrutural"]      = '';
                $arRecordSet[$inCount]  ["nivel"]               = '';
                $arRecordSet[$inCount]  ["nom_conta"]           = 'RECEITA EXTRA-ORÇAMENTÁRIA';
                $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
                $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
                $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
                $arRecordSet[++$inCount]["vl_saldo_atual"]      = '';

                //Espaço
                $arRecordSet[$inCount]  ["cod_estrutural"]      = '';
                $arRecordSet[$inCount]  ["nivel"]               = '';
                $arRecordSet[$inCount]  ["nom_conta"]           = '';
                $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
                $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
                $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
                $arRecordSet[++$inCount]["vl_saldo_atual"]      = '';
            }
            $cod_conta_anterior = $cod_conta_atual;
            $arRecordSet[$inCount]  ["cod_estrutural"]      = $rsRecordSet->getCampo("cod_estrutural");
            $arRecordSet[$inCount]  ["nivel"]               = $rsRecordSet->getCampo("nivel");
            $arRecordSet[$inCount]  ["nom_conta"]           = $rsRecordSet->getCampo("nom_conta");
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = $rsRecordSet->getCampo("vl_saldo_anterior","CONTABIL");
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = $rsRecordSet->getCampo("vl_saldo_debitos");
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = $rsRecordSet->getCampo("vl_saldo_creditos");
            $arRecordSet[$inCount++]["vl_saldo_atual"]      = $rsRecordSet->getCampo("vl_saldo_atual");
            if ( $rsRecordSet->getCampo("nivel") >= 5 ) {
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

            //Total Geral da Despesa
            $arRecordSet[$inCount]  ["cod_estrutural"]      = '';
            $arRecordSet[$inCount]  ["nivel"]               = '';
            $arRecordSet[$inCount]  ["nom_conta"]           = 'TOTAL GERAL DA DESPESA';
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = $nuSaldoAnterior;
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = $nuSaldoDebitos;
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = $nuSaldoCreditos;
            $arRecordSet[$inCount]  ["vl_saldo_atual"]      = $nuSaldoAtual;

            //Espaço
            $inCount++;
            $arRecordSet[$inCount]  ["cod_estrutural"]      = '';
            $arRecordSet[$inCount]  ["nivel"]               = '';
            $arRecordSet[$inCount]  ["nom_conta"]           = '';
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
            $arRecordSet[$inCount]["vl_saldo_atual"]      = '';

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

                $arRecordSet[$inCount]  ["nom_conta"]           = $rsEntidades->getCampo('nom_cgm');
                $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
                $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
                $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
                $arRecordSet[$inCount]  ["vl_saldo_atual"]      = '';
            }

            //Espaço
            $inCount++;
            $arRecordSet[$inCount]  ["nom_conta"]           = '';
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '';
            $arRecordSet[$inCount]["vl_saldo_atual"]      = '';

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
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '                                    ____________________________________________';
            $arRecordSet[$inCount]  ["vl_saldo_atual"]      = '';

            $inCount++;
            $arRecordSet[$inCount]  ["nom_conta"]			= '                                                        Assinatura do Representante';
            $arRecordSet[$inCount]  ["vl_saldo_anterior"]   = '';
            $arRecordSet[$inCount]  ["vl_saldo_debitos"]    = '';
            $arRecordSet[$inCount]  ["vl_saldo_creditos"]   = '                       Assinatura do Contador Responsável             ';
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
