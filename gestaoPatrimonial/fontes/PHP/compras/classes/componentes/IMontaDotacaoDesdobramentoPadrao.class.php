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
    * Componente utilizado para montar os combos de Desdobramento e Dotação.
    * Data de Criação: 08/09/2006

    * @author Analista      : Diego Barbosa Victoria
    * @author Desenvolvedor : Rodrigo

    * @package URBEM
    * @subpackage

    * Casos de uso: uc-03.04.01

    $Id: IMontaDotacaoDesdobramento.class.php 32939 2008-09-03 21:14:50Z domluc $
*/

include_once ( CLA_OBJETO );
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php"   					   );

class  IMontaDotacaoDesdobramentoPadrao extends Objeto
{
    /**
    * @access Public;
    * @var Reference Object
    **/
    public $obForm;
    /**
    * @access Public;
    * @var Boolean
    **/
    public $boMostraSintetico;
    /**
    * @access Public;
    * @var Boolean
    **/
    public $boReserva;
    

    public function setMostraSintetico($valor) { $this->boMostraSintetico = $valor; }
    public function getMostraSintetico()       { return $this->boMostraSintetico;}
    
    public function setConsideraReserva($valor) { $this->boReserva = $valor; }
    public function getConsideraReserva()       { return $this->boReserva;}
    
    public function IMontaDotacaoDesdobramentoPadrao()
    {
        include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php" );
        parent::Objeto();

        $obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;
        $rsClassificacaoPadrao              = new RecordSet;
        $obREmpenhoAutorizacaoEmpenho->checarFormaExecucaoOrcamento( $stFormaExecucao );

        $obTConfiguracao = new TAdministracaoConfiguracao();
        $obTConfiguracao->setDado('exercicio',Sessao::getExercicio());
        $obTConfiguracao->pegaConfiguracao( $this->boFormaExecucao,'forma_execucao_orcamento' );
        $this->boFormaExecucao = ( $this->boFormaExecucao == 1 ) ? true : false;

        $this->obBscDespesa = new BuscaInner;
        $this->obBscDespesa->setRotulo               ( "Dotação Orçamentária"            );
        $this->obBscDespesa->setTitle                ( "Informe a dotação orcamentária padrão." );
        $this->obBscDespesa->setNull                 ( true                              );
        $this->obBscDespesa->setId                   ( "stNomDespesaPadrao"                    );
        $this->obBscDespesa->setValue                ( $stNomDespesaPadrao                     );
        $this->obBscDespesa->obCampoCod->setName     ( "inCodDespesaPadrao"                    );
        $this->obBscDespesa->obCampoCod->setId       ( "inCodDespesaPadrao"                    );
        $this->obBscDespesa->obCampoCod->setSize     ( 10                                );
        $this->obBscDespesa->obCampoCod->setMaxLength( 5                                 );
        $this->obBscDespesa->obCampoCod->setValue    ( $inCodDespesaPadrao                     );
        $this->obBscDespesa->obCampoCod->setAlign    ("left"                             );

        $this->obBscDespesa->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."despesa/FLDespesa2.php','frm','inCodDespesaPadrao','stNomDespesaPadrao','autorizacaoEmpenho&inCodEntidade='+document.frm.inCodEntidade.value+'&inCodCentroCusto='+document.frm.inCodCentroCusto.value,'".Sessao::getId()."','800','550');");

        //Define o Hidden para armazenar o cod despesa antes de abrir a popup para alterar o mesmo
        $this->obHdnCodDespesa = new Hidden;
        $this->obHdnCodDespesa->setName   ( "inCodDespesaAnteriorPadrao" );
        $this->obHdnCodDespesa->setId     ( "inCodDespesaAnteriorPadrao" );
        $this->obHdnCodDespesa->setValue  ( ""                     );

        $this->obHdnCodClassificacao = new Hidden();
        $this->obHdnCodClassificacao->setName	( 'HdnCodClassificacaoPadrao' );
        $this->obHdnCodClassificacao->setId 	( 'HdnCodClassificacaoPadrao' );
        $this->obHdnCodClassificacao->setValue 	( ''					);

        if ($this->boFormaExecucao) {
            // Define Objeto Select para Classificacao da Despesa
            $this->obCmbClassificacao = new Select;
            $this->obCmbClassificacao->setRotulo     ( "Desdobramento"                   );
            $this->obCmbClassificacao->setTitle      ( "Informe a rubrica de despesa padrão."   );
            $this->obCmbClassificacao->setName       ( "stCodClassificacaoPadrao"              );
            $this->obCmbClassificacao->setId         ( "stCodClassificacaoPadrao"              );
            $this->obCmbClassificacao->setValue      ( ""                                );
            $this->obCmbClassificacao->setStyle      ( "width: 600"                      );
            // $this->obCmbClassificacao->setNull       ( ($stFormaExecucao) ? false : true );
            $this->obCmbClassificacao->setDisabled   ( ($stFormaExecucao) ? false : true );
            $this->obCmbClassificacao->addOption     ( "", "Selecione"                   );
            $this->obCmbClassificacao->setCampoId    ( "cod_estrutural_padrao"                  );
            $this->obCmbClassificacao->setCampoDesc  ( "cod_estrutural_padrao"                  );
            $this->obCmbClassificacao->preencheCombo ( $rsClassificacaoPadrao                  );

        }

        // Define objeto Label para saldo anterior
        $this->obLblSaldoDotacao = new Label;
        $this->obLblSaldoDotacao->setId    ( "nuSaldoDotacaoPadrao" 		);
        $this->obLblSaldoDotacao->setValue ( $nuSaldoDotacaoPadrao  		);
        $this->obLblSaldoDotacao->setRotulo( "Saldo da Dotação" 	);

        $this->obHdnSaldoDotacao = new hidden();
        $this->obHdnSaldoDotacao->setId('nuHdnSaldoDotacaoPadrao');
        $this->obHdnSaldoDotacao->setName('nuHdnSaldoDotacaoPadrao');
        $this->obHdnSaldoDotacao->setValue($nuSaldoDotacaoPadrao);

        // Define Objeto Span Para lista de itens
        $this->obSpanSaldo = new Span;
        $this->obSpanSaldo->setId( "spnSaldoDotacao" );

        $this->setMostraSintetico( false );

    }

    public function geraFormulario(&$obFormulario)
    {

        $stParams = Sessao::getId();
        
        if ( $this->getMostraSintetico() ) {
            $stParams .= '&boMostraSintetico=true';
        }
        
        if ( $this->getConsideraReserva() == true) {
            $stParams .= '&boConsideraReserva=true';
        } else {
            $stParams .= '&boConsideraReserva=false';
        }
        
        $js = " if (this.value!=document.frm.inCodDespesaAnteriorPadrao.value) {         ";
        $js.= "   document.frm.inCodDespesaAnteriorPadrao.value=this.value;              ";
        //       $js.= "   BloqueiaFrames(true,false);                                   ";
        $js.= "   var stTarget = document.frm.target;                                    ";
        $js.= "   var stAction = document.frm.action;                                                                     ";
        $js.= "   document.frm.stCtrl.value = 'buscaDespesaDiverso';                                                      "; 
        $js.= "   document.getElementById('stNomDespesaPadrao').innerHTML = '&nbsp;';                                     ";
        $js.= "   document.frm.target ='oculto';                                                                          ";
        $js.= "   document.frm.action ='../../instancias/processamento/OCIMontaDotacaoDesdobramentoPadrao.php?".$stParams."';";
        $js.= "   document.frm.submit();                                                                                  ";
        $js.= "   document.frm.action = '".$pgOcul."?".Sessao::getId()."';                                                ";
        $js.= "   document.frm.action = stAction;                                                                         ";
        $js.= "   document.frm.target = stTarget;                                                                         ";
        $js.= " } ";

        $this->obBscDespesa->obCampoCod->obEvento->setOnBlur($js);
        
        $obFormulario->addHidden    ( $this->obHdnCodDespesa    );
        $obFormulario->addHidden    ( $this->obHdnSaldoDotacao  );
        $obFormulario->addHidden    ( $this->obHdnCodClassificacao );
        $obFormulario->addComponente( $this->obBscDespesa       );
        if ($this->boFormaExecucao) {
            $obFormulario->addComponente( $this->obCmbClassificacao );
        }
        $obFormulario->addComponente( $this->obLblSaldoDotacao  );
        $obFormulario->addSpan      ( $this->obSpanSaldo        );
    }
}

?>