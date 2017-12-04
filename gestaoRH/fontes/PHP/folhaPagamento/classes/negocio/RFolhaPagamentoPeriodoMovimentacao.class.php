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
* Classe de Regra de Negócio Folha Pagamento Periodo Movimentacao
* Data de Criação   : 24/10/2005

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @package URBEM
* @subpackage regra

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2008-02-13 13:27:10 -0200 (Qua, 13 Fev 2008) $

* Casos de uso: uc-04.05.40
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                   );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacaoSituacao.class.php"           );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php"                  );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalculoFolhaPagamento.class.php"                    );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                            );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaComplementar.class.php"                        );

class RFolhaPagamentoPeriodoMovimentacao
{
    /**
    * @var Integer
    * @access Private
    */
    public $inCodPeriodoMovimentacao;
    /**
    * @var Date
    * @access Private
    */
    public $dtInicial;
    /**
    * @var Date
    * @access Private
    */
    public $dtFinal;
    /**
    * @var Array
    * @access Private
    */
    public $arRFolhaPagamentoPeriodoContratoServidor;
    /**
    * @var Object
    * @access Private
    */
    public $roRFolhaPagamentoPeriodoContratoServidor;
    /**
    * @var Array
    * @access Private
    */
    public $arRFolhaPagamentoCalculoFolhaPagamento;
    /**
    * @var Object
    * @access Private
    */
    public $roRFolhaPagamentoCalculoFolhaPagamento;
    /**
    * @var Object
    * @access Private
    */
    public $obRFolhaPagamentoFolhaSituacao;
    /**
    * @var Array
    * @access Private
    */
    public $arRFolhaPagamentoFolhaComplementar;
    /**
    * @var Object
    * @access Private
    */
    public $roRFolhaPagamentoFolhaComplementar;
    /**
    * @var Boolean
    * @access Private
    */
    public $boTransacao;

    /**
    * @access Public
    * @param Object $valor
    */
    public function setCodPeriodoMovimentacao($valor) { $this->inCodPeriodoMovimentacao = $valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setDtInicial($valor) { $this->dtInicial = $valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setDtFinal($valor) { $this->dtFinal = $valor; }
    /**
    * @access Public
    * @param Array $valor
    */
    public function setARRFolhaPagamentoPeriodoContratoServidor($valor) { $this->arRFolhaPagamentoPeriodoContratoServidor = $valor; }
     /**
    * @access Public
    * @param Object $valor
    */
    public function setRORFolhaPagamentoPeriodoContratoServidor(&$valor) { $this->roRFolhaPagamentoPeriodoContratoServidor = &$valor; }
    /**
    * @access Public
    * @param Array $valor
    */
    public function setARRFolhaPagamentoCalculoFolhaPagamento($valor) { $this->arRFolhaPagamentoCalculoFolhaPagamento = $valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setRORFolhaPagamentoCalculoFolhaPagamento(&$valor) { $this->roRFolhaPagamentoCalculoFolhaPagamento = &$valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setRFolhaPagamentoFolhaSituacao($valor) { $this->obRFolhaPagamentoFolhaSituacao = $valor; }
    /**
    * @access Public
    * @param Array $valor
    */
    public function setARRFolhaPagamentoFolhaComplementar($valor) { $this->arRFolhaPagamentoFolhaComplementar = $valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setRORFolhaPagamentoFolhaComplementar(&$valor) { $this->roRFolhaPagamentoFolhaComplementar = &$valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setTransacao($valor) { $this->obTransacao           = $valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setTFolhaPagamentoPeriodoMovimentacao($valor) { $this->obTFolhaPagamentoPeriodoMovimentacao = $valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setTFolhaPagamentoPeriodoMovimentacaoSituacao($valor) { $this->obTFolhaPagamentoPeriodoMovimentacaoSituacao = $valor; }

    /**
    * @access Public
    * @return Integer
    */
    public function getCodPeriodoMovimentacao() { return $this->inCodPeriodoMovimentacao; }
    /**
    * @access Public
    * @return Date
    */
    public function getDtInicial() { return $this->dtInicial; }
    /**
    * @access Public
    * @return Date
    */
    public function getDtFinal() { return $this->dtFinal;   }
    /**
    * @access Public
    * @return Array
    */
    public function getARRFolhaPagamentoPeriodoContratoServidor() { return $this->arRFolhaPagamentoPeriodoContratoServidor; }
    /**
    * @access Public
    * @return Object
    */
    public function getRORFolhaPagamentoPeriodoContratoServidor() { return $this->roRFolhaPagamentoPeriodoContratoServidor; }
    /**
    * @access Public
    * @return Array
    */
    public function getARRFolhaPagamentoCalculoFolhaPagamento() { return $this->arRFolhaPagamentoCalculoFolhaPagamento; }
    /**
    * @access Public
    * @return Object
    */
    public function getRORFolhaPagamentoCalculoFolhaPagamento() { return $this->roRFolhaPagamentoCalculoFolhaPagamento; }
    /**
    * @access Public
    * @return Object
    */
    public function getRFolhaPagamentoFolhaSituacao() { return $this->obRFolhaPagamentoFolhaSituacao; }
    /**
    * @access Public
    * @return Object
    */
    public function getARRFolhaPagamentoFolhaComplementar() { return $this->arRFolhaPagamentoFolhaComplementar; }
    /**
    * @access Public
    * @return Object
    */
    public function getRORFolhaPagamentoFolhaComplementar() { return $this->roRFolhaPagamentoFolhaComplementar; }

    /**
    * Método Construtor
    * @access Private
    */
    public function RFolhaPagamentoPeriodoMovimentacao()
    {
        $this->setTFolhaPagamentoPeriodoMovimentacao            ( new TFolhaPagamentoPeriodoMovimentacao            );
        $this->setTFolhaPagamentoPeriodoMovimentacaoSituacao    ( new TFolhaPagamentoPeriodoMovimentacaoSituacao    );
        $this->setTransacao                                     ( new Transacao                                     );
        $this->setRFolhaPagamentoFolhaSituacao                  ( new RFolhaPagamentoFolhaSituacao( $this )         );
    }

    public function listar(&$rsLista, $stFiltro="", $stOrdem = "", $boTransacao = "")
    {
        $obErro = $this->obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsLista, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function listarPeriodoMovimentacao(&$rsLista, $stFiltro="", $stOrdem = "", $boTransacao = "")
    {
        if ( $this->getDtFinal() ) {
            $stFiltro .= "AND to_char(FPM.dt_final, 'yyyy-mm-dd')                    like '".$this->getDtFinal()."%' \n";
        }
        $obErro = $this->obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsLista, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function listarUltimaMovimentacao(&$rsUltimaMovimentacao, $boTransacao = "")
    {
        $stFiltro = "";
        $stOrdem = "";
        $obErro = $this->obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function listarUltimaMovimentacaoFechada(&$rsUltimaMovimentacao, $boTransacao = "")
    {
        $obErro = $this->obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacaoFechada($rsUltimaMovimentacao, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function abrirPeriodoMovimentacao($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        $this->obTFolhaPagamentoPeriodoMovimentacao->setDado( "dt_inicial"              , $this->getDtInicial() );
        $this->obTFolhaPagamentoPeriodoMovimentacao->setDado( "dt_final"                , $this->getDtFinal()   );
        $this->obTFolhaPagamentoPeriodoMovimentacao->setDado( "exercicio"               , Sessao::getExercicio()    );
        $this->obTFolhaPagamentoPeriodoMovimentacao->setDado( "cod_entidade"            , Sessao::getEntidade()    );
        $obErro = $this->obTFolhaPagamentoPeriodoMovimentacao->abrirPeriodoMovimentacao($boTransacao);
        
        //Validar configuracao se deve realizar o adiatamento do 13 no mes do aniversario do servidor
        //Gestão Recursos Humanos :: Folha de Pagamento :: Configuração :: Configurar Cálculo de 13º Salário
        if ( !$obErro->ocorreu() ) {
            $boAdiantamenteMesAniversario = SistemaLegado::pegaConfiguracao('adiantamento_13_salario'.Sessao::getEntidade(),27,Sessao::getExercicio(), $boTransacao );
            if ( $boAdiantamenteMesAniversario == 'true') {
                $obErro = $this->gerarAdiantamento13MesAniversario($boTransacao);
            }
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoPeriodoMovimentacao );

        return $obErro;
    }

    public function fecharPeriodoMovimentacao($boTransacao = "")
    {
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->listarUltimaMovimentacao($rsUltimaMovimentacao,$boTransacao);
            $this->obTFolhaPagamentoPeriodoMovimentacaoSituacao->setDado( "cod_periodo_movimentacao", $rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao') );
            $this->obTFolhaPagamentoPeriodoMovimentacaoSituacao->setDado( "situacao", "f" );
            $this->obTFolhaPagamentoPeriodoMovimentacaoSituacao->inclusao($boTransacao);
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoPeriodoMovimentacaoSituacao );

        return $obErro;
    }

    public function cancelarPeriodoMovimentacao($boTransacao = "")
    {
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        //Validar configuracao se deve realizar o adiatamento do 13 no mes do aniversario do servidor
        //Gestão Recursos Humanos :: Folha de Pagamento :: Configuração :: Configurar Cálculo de 13º Salário
        if ( !$obErro->ocorreu() ) {
            $boAdiantamenteMesAniversario = SistemaLegado::pegaConfiguracao('adiantamento_13_salario'.Sessao::getEntidade(),27,Sessao::getExercicio(), $boTransacao );
            if ( $boAdiantamenteMesAniversario == 'true') {
                $obErro = $this->cancelarAdiantamento13MesAniversario($boTransacao);
            }
        }
        $this->obTFolhaPagamentoPeriodoMovimentacao->setDado( "cod_entidade", Sessao::getEntidade()    );
        $obErro = $this->obTFolhaPagamentoPeriodoMovimentacao->cancelarPeriodoMovimentacao($boTransacao);
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoPeriodoMovimentacao );

        return $obErro;
    }

    public function addRFolhaPagamentoPeriodoContratoServidor()
    {
        $this->arRFolhaPagamentoPeriodoContratoServidor[] = new RFolhaPagamentoPeriodoContratoServidor( $this );
        $this->roRFolhaPagamentoPeriodoContratoServidor = &$this->arRFolhaPagamentoPeriodoContratoServidor[ count($this->arRFolhaPagamentoPeriodoContratoServidor)-1 ];
    }

    public function addRFolhaPagamentoCalculoFolhaPagamento()
    {
        //$this->arRFolhaPagamentoCalculoFolhaPagamento[] = new RFolhaPagamentoCalculoFolhaPagamento();
        //$this->roRFolhaPagamentoCalculoFolhaPagamento = &$this->arRFolhaPagamentoCalculoFolhaPagamento[ count($this->arRFolhaPagamentoCalculoFolhaPagamento)-1 ];
        $this->roRFolhaPagamentoCalculoFolhaPagamento = new RFolhaPagamentoCalculoFolhaPagamento();
        $this->roRFolhaPagamentoCalculoFolhaPagamento->setRORFolhaPagamentoPeriodoMovimentacao($this);
    }

    public function addRFolhaPagamentoFolhaComplementar()
    {
        $this->arRFolhaPagamentoFolhaComplementar[] = new RFolhaPagamentoFolhaComplementar( $this );
        $this->roRFolhaPagamentoFolhaComplementar = &$this->arRFolhaPagamentoFolhaComplementar[ count($this->arRFolhaPagamentoFolhaComplementar)-1 ];
    }

    public function recuperaAnosPeriodoMovimentacao(&$rsUltimaMovimentacao, $stFiltro, $boTransacao = '')
    {
        $obErro = $this->obTFolhaPagamentoPeriodoMovimentacao->recuperaAnosPeriodoMovimentacao($rsUltimaMovimentacao, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }

    public function gerarAdiantamento13MesAniversario($boTransacao = '')
    {
        $obErro = new Erro();
        $obErro = $this->obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao,"","",$boTransacao);
        $arDtFinal = explode("/",$rsUltimaMovimentacao->getCampo("dt_final"));
        if($obErro->ocorreu()) return $obErro;  
        
        //Valida configuracao e datas do mes para executar esta rotina
        $boValida = $this->validaAdiantamento13MesAniversario($arDtFinal,$boTransacao);        
        if(!$boValida) return $obErro;

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConcessaoDecimo.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoGeraRegistroDecimo.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoAdiantamento.class.php");
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        
        $obTPessoalContrato = new TPessoalContrato();
        $obTFolhaPagamentoConcessaoDecimo = new TFolhaPagamentoConcessaoDecimo();
        $obTFolhaPagamentoConcessaoDecimo->setDado( "cod_periodo_movimentacao", $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao") );
        $obTFolhaPagamentoConcessaoDecimo->setDado( "mes_aniversario", $arDtFinal[1]);
        $obErro = $obTFolhaPagamentoConcessaoDecimo->recuperaContratosAdiantamentoDecidoMesAniversario($rsContratos,"","",$boTransacao);
        
        if($obErro->ocorreu()) return $obErro;
        
        $stFiltro  = " WHERE to_char(dt_final,'yyyy') = '".$arDtFinal[2]."'";
        $stOrdem  = " cod_periodo_movimentacao LIMIT 1";
        $this->obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsTodosPeriodos,$stFiltro,$stOrdem,$boTransacao);        
        $obFFolhaPagamentoGeraRegistroDecimo = new FFolhaPagamentoGeraRegistroDecimo();
        $obTFolhaPagamentoConcessaoDecimo = new TFolhaPagamentoConcessaoDecimo();
        $obTFolhaPagamentoConfiguracaoAdiantamento = new TFolhaPagamentoConfiguracaoAdiantamento();
        $obTFolhaPagamentoConfiguracaoAdiantamento->obTFolhaPagamentoConcessaoDecimo = &$obTFolhaPagamentoConcessaoDecimo;
        $arContratos = array();
        $arContratosErro = array();
        
        while (!$rsContratos->eof()) {
            if($obErro->ocorreu()) return $obErro;
                
            $stFiltro = " AND contrato.cod_contrato = ".$rsContratos->getCampo("cod_contrato");
            $obErro = $obTPessoalContrato->recuperaCgmDoRegistro($rsContrato,$stFiltro,'',$boTransacao);
            if($obErro->ocorreu()) return $obErro;

            $obTFolhaPagamentoConcessaoDecimo->setDado("cod_contrato",$rsContratos->getCampo("cod_contrato"));
            $obTFolhaPagamentoConcessaoDecimo->setDado("desdobramento",'A');
            $obTFolhaPagamentoConcessaoDecimo->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTFolhaPagamentoConcessaoDecimo->setDado("folha_salario",true);

            $stFiltro  = " WHERE cod_contrato = ".$rsContratos->getCampo("cod_contrato");
            $stFiltro .= "   AND cod_periodo_movimentacao <= ".$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao");
            $stFiltro .= "   AND cod_periodo_movimentacao >=".$rsTodosPeriodos->getCampo("cod_periodo_movimentacao");
            $stFiltro .= "   AND desdobramento = 'A' ";
            $obErro = $obTFolhaPagamentoConcessaoDecimo->recuperaTodos($rsConcessao,$stFiltro,'',$boTransacao);            
            if($obErro->ocorreu()) return $obErro;

            if ( $rsConcessao->getNumLinhas() < 0 ) {
                $obTFolhaPagamentoConfiguracaoAdiantamento->setDado("percentual"      ,50.00);
                $obTFolhaPagamentoConfiguracaoAdiantamento->setDado("vantagens_fixas" ,false);
                $obTFolhaPagamentoConfiguracaoAdiantamento->setDado("desdobramento"   ,'A');
                $obTFolhaPagamentoConfiguracaoAdiantamento->setDado("cod_contrato"    ,$rsContratos->getCampo("cod_contrato"));
                $obTFolhaPagamentoConfiguracaoAdiantamento->setDado("cod_periodo_movimentacao" ,$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));

                $obErro = $obTFolhaPagamentoConcessaoDecimo->inclusao($boTransacao);
                if($obErro->ocorreu()) return $obErro;

                $obErro = $obTFolhaPagamentoConfiguracaoAdiantamento->inclusao($boTransacao);
                if($obErro->ocorreu()) return $obErro;

                $inIndex = count($arContratos);
                $arContratos[$inIndex]['registro'] = $rsContrato->getCampo("registro");
                $arContratos[$inIndex]['numcgm']   = $rsContrato->getCampo("numcgm");
                $arContratos[$inIndex]['nom_cgm']  = $rsContrato->getCampo("nom_cgm");

                $obFFolhaPagamentoGeraRegistroDecimo->setDado("cod_contrato"            ,$rsContratos->getCampo("cod_contrato"));
                $obFFolhaPagamentoGeraRegistroDecimo->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
                $obFFolhaPagamentoGeraRegistroDecimo->setDado("desdobramento"           ,'A');
                $obErro = $obFFolhaPagamentoGeraRegistroDecimo->geraRegistroDecimo($rsGerar,$boTransacao);
                if($obErro->ocorreu()) return $obErro;

            } else {
                $inIndex = count($arContratosErro);
                $arContratosErro[$inIndex]['registro']     = $rsContrato->getCampo("registro");
                $arContratosErro[$inIndex]['numcgm']       = $rsContrato->getCampo("numcgm");
                $arContratosErro[$inIndex]['nom_cgm']      = $rsContrato->getCampo("nom_cgm");
                $arContratosErro[$inIndex]['motivo']       = "A matrícula já possui concessão de adiantamento de 13º, no exercício";
            }
            
            $rsContratos->proximo();
        }

        return $obErro;
        
    }

    public function cancelarAdiantamento13MesAniversario($boTransacao = '')
    {   
        $obErro = new Erro();
        
        $obErro = $this->obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao,"","",$boTransacao);
        $arDtFinal = explode("/",$rsUltimaMovimentacao->getCampo("dt_final"));
        if($obErro->ocorreu()) return $obErro;
        
        //Valida configuracao e datas do mes para executar esta rotina
        $boValida = $this->validaAdiantamento13MesAniversario($arDtFinal,$boTransacao);
        if(!$boValida) return $obErro;

        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConcessaoDecimo.class.php");
        $obTPessoalContrato = new TPessoalContrato();
        //Busca todos os contratos que foram configurados com adiantamento do 13 no mes do aniversario
        $obTFolhaPagamentoConcessaoDecimo = new TFolhaPagamentoConcessaoDecimo();
        $obTFolhaPagamentoConcessaoDecimo->setDado( "cod_periodo_movimentacao", $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao") );
        $obTFolhaPagamentoConcessaoDecimo->setDado( "mes_aniversario", $arDtFinal[1]);
        $obErro = $obTFolhaPagamentoConcessaoDecimo->recuperaContratosAdiantamentoDecidoMesAniversario($rsContratos,"","",$boTransacao);
        
        if($obErro->ocorreu()) return $obErro;
        
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDeducaoDependente.class.php");
        $obTFolhaPagamentoDeducaoDependente = new TFolhaPagamentoDeducaoDependente();
        $arCodContratos = array();
                
        while (!$rsContratos->eof()) {
            $obTPessoalContrato = new TPessoalContrato;
            $stFiltro = " AND contrato.cod_contrato = ".$rsContratos->getCampo("cod_contrato");
            $obErro = $obTPessoalContrato->recuperaCgmDoRegistro($rsContrato,$stFiltro,'',$boTransacao);
            
            if($obErro->ocorreu()) return $obErro;

            $stFiltro = " WHERE deducao_dependente.cod_contrato = ".$rsContratos->getCampo("cod_contrato");
            $stFiltro .= "   AND deducao_dependente.cod_periodo_movimentacao = ".$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao");
            $stFiltro .= "   AND deducao_dependente.cod_tipo = 4";
            $obTFolhaPagamentoDeducaoDependente->recuperaTodos($rsDeducaoDependente,$stFiltro,'',$boTransacao);
            if ($rsDeducaoDependente->getNumLinhas() == 1) {
                $arCodContratos[] = array("cod_contrato"=>$rsContratos->getCampo("cod_contrato"));
            }
            $obErro = $this->deletarConcessaoDecimo($rsContratos->getCampo("cod_contrato"),$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"),$boTransacao);
            if($obErro->ocorreu()) return $obErro;
                    
            $rsContratos->proximo();
        }

        if (!$obErro->ocorreu()) {
            //Recalculo do contrato
            $rsContratos = new recordset;
            $rsContratos->preenche($arCodContratos);
            $obErro = $this->recalcularSalario($rsContratos,$boTransacao);
        }
    
        return $obErro;
    }


    function deletarConcessaoDecimo($inCodContrato,$inCodPeriodoMovimentacao,$boTransacao)
    {
        $obErro = new Erro();
    
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEventoDecimo.class.php");
        $obTFolhaPagamentoUltimoRegistroEventoDecimo =  new TFolhaPagamentoUltimoRegistroEventoDecimo;
        $stFiltro  = " AND cod_contrato = ".$inCodContrato;
        $stFiltro .= " AND cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
        $obErro = $obTFolhaPagamentoUltimoRegistroEventoDecimo->recuperaRegistrosEventoDecimoDoContrato($rsRegistros,$stFiltro,'',$boTransacao);
        if($obErro->ocorreu()) return $obErro;
    
        while (!$rsRegistros->eof()) {
            $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("cod_registro",$rsRegistros->getCampo("cod_registro"));
            $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("cod_evento",$rsRegistros->getCampo("cod_evento"));
            $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("desdobramento",$rsRegistros->getCampo("desdobramento"));
            $obTFolhaPagamentoUltimoRegistroEventoDecimo->setDado("timestamp",$rsRegistros->getCampo("timestamp"));
            $obErro = $obTFolhaPagamentoUltimoRegistroEventoDecimo->deletarUltimoRegistroEvento($boTransacao);
            if($obErro->ocorreu()) return $obErro;
    
            $rsRegistros->proximo();
        }
    
        //Exclusão dos contratos com pagamento de décimo em salário
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConcessaoDecimo.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoAdiantamento.class.php");
        $obTFolhaPagamentoConcessaoDecimo = new TFolhaPagamentoConcessaoDecimo();
        $obTFolhaPagamentoConfiguracaoAdiantamento = new TFolhaPagamentoConfiguracaoAdiantamento();
        $obTFolhaPagamentoConfiguracaoAdiantamento->obTFolhaPagamentoConcessaoDecimo = &$obTFolhaPagamentoConcessaoDecimo;
    
        $stFiltro  = " WHERE cod_contrato = ".$inCodContrato;
        $stFiltro .= "   AND cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
        $stFiltro .= "   AND desdobramento = 'A' ";
        $stFiltro .= "   AND folha_salario IS TRUE ";
        $obErro = $obTFolhaPagamentoConcessaoDecimo->recuperaTodos($rsConcessoDecimo,$stFiltro,'',$boTransacao);        
        if($obErro->ocorreu()) return $obErro;
    
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEvento.class.php");
        $obTFolhaPagamentoUltimoRegistroEvento = new TFolhaPagamentoUltimoRegistroEvento();
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
        $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado();
        while (!$rsConcessoDecimo->eof()) {
            $stFiltro  = "   AND cod_contrato =".$inCodContrato;
            $stFiltro .= "   AND cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao;
            $stFiltro .= "   AND desdobramento = 'I'";
            $obErro = $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculados($rsEventosCalculados,$stFiltro,'',$boTransacao);            
            if($obErro->ocorreu()) return $obErro;
    
            while (!$rsEventosCalculados->eof()) {
                $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_registro" , $rsEventosCalculados->getCampo("cod_registro"));
                $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_evento"   , $rsEventosCalculados->getCampo("cod_evento"));
                $obTFolhaPagamentoUltimoRegistroEvento->setDado("desdobramento", $rsEventosCalculados->getCampo("desdobramento"));
                $obTFolhaPagamentoUltimoRegistroEvento->setDado("timestamp"    , $rsEventosCalculados->getCampo("timestamp"));
                $obErro = $obTFolhaPagamentoUltimoRegistroEvento->deletarUltimoRegistroEvento($boTransacao);
                if($obErro->ocorreu()) return $obErro;
    
                $rsEventosCalculados->proximo();
            }
            $rsConcessoDecimo->proximo();
        }
    
        $obTFolhaPagamentoConcessaoDecimo->setDado("cod_contrato",$inCodContrato);
        $obTFolhaPagamentoConcessaoDecimo->setDado("cod_periodo_movimentacao",$inCodPeriodoMovimentacao);
        $obErro = $obTFolhaPagamentoConfiguracaoAdiantamento->exclusao($boTransacao);
        if($obErro->ocorreu()) return $obErro;
    
        $obErro = $obTFolhaPagamentoConcessaoDecimo->exclusao($boTransacao);
    
        return $obErro;
    }

    public function validaAdiantamento13MesAniversario($arDtFinal,$boTransacao = '')
    {
        $boValida = true;
        $inMesSaldo13 = SistemaLegado::pegaConfiguracao('mes_calculo_decimo'.Sessao::getEntidade(),27,Sessao::getExercicio(), $boTransacao );
        //Não deve ser executada essa ação nos meses de dezembro e novembro 
        //só se a configuracao do saldo estiver em dezembro
        if ( $arDtFinal[1] == 12 ){
            return false;
        }elseif( ($arDtFinal[1] == 11) && ($arDtFinal[1] == $inMesSaldo13) ){
            return false;
        }
        return $boValida;
    }

    function recalcularSalario($rsContratos,$boTransacao)
    {
        $obErro = new Erro();
        //Recalcula folha salário de contratos com dependente
        //isso serve para no caso do cancelamento de um décimo onde está
        //sendo incorporado a dedução de dependente, essa dedução passe para
        //a folha salário do contrato
        $stCodContratos = "";
        while (!$rsContratos->eof()) {
            $stCodContratos .= $rsContratos->getCampo("cod_contrato").",";
            $rsContratos->proximo();
        }
        $stCodContratos = substr($stCodContratos,0,strlen($stCodContratos)-1);
    
        include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoDeletarInformacoesCalculo.class.php");
        $obFFolhaPagamentoDeletarInformacoesCalculo = new FFolhaPagamentoDeletarInformacoesCalculo();
        $obFFolhaPagamentoDeletarInformacoesCalculo->setDado("stTipoFolha"          ,"S"            );
        $obFFolhaPagamentoDeletarInformacoesCalculo->setDado("inCodComplementar"    ,0              );
        $obFFolhaPagamentoDeletarInformacoesCalculo->setDado("stCodContratos"       ,$stCodContratos );
        $obErro = $obFFolhaPagamentoDeletarInformacoesCalculo->deletarInformacoesCalculo($rsDeletar, $boTransacao);
        if($obErro->ocorreu()) return $obErro;
    
        include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoCalculaFolha.class.php");
        $obFFolhaPagamentoCalculaFolha = new FFolhaPagamentoCalculaFolha();
        $rsContratos->setPrimeiroElemento();
        while ( !$rsContratos->eof() ) {
            $obFFolhaPagamentoCalculaFolha->setDado('cod_contrato',$rsContratos->getCampo("cod_contrato"));
            $obFFolhaPagamentoCalculaFolha->setDado('boErro','f');
            $obErro = $obFFolhaPagamentoCalculaFolha->calculaFolha($rsCalcula, $boTransacao);
            if($obErro->ocorreu()) return $obErro;
            $rsContratos->proximo();
        }
    
        return $obErro;
    }

}//END OF CLASS
