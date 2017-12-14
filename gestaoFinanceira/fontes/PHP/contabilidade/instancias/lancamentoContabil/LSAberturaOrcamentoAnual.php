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
    * Abertura Orcamento Anual
    * Data de Criação   : 13/08/2013
    * @author Analista: Valtair
    * @author Desenvolvedor: Evandro Melos
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "AberturaOrcamentoAnual";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

$boTransacao = new Transacao();
$stFiltro = "";
$stJs = "jQuery('#nuValor_2').hide();";
$stJs .=" jQuery(\"input[name^='nuValor_']\").each(function(){ 
                                                    if (jQuery(this).val() == '') {
                                                        jQuery(this).val('0,00');
                                                    }
                                                });";
$jsOnLoad = $stJs;

$obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;
$obTContabilidadePlanoAnalitica  = new TContabilidadePlanoAnalitica;
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setExercicio      ( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade ( $_POST['inCodEntidade'] );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->consultar( $rs );
$stNomEntidade = $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->obRCGM->getNomCGM();

//Buscar valores de lancamentos de Abertura de Orcamento Anterior
$obTContabilidadePlanoAnalitica->setDado( "exercicio"    , Sessao::getExercicio() );
$obTContabilidadePlanoAnalitica->setDado( "cod_entidade" , $_POST['inCodEntidade'] );
$obTContabilidadePlanoAnalitica->recuperaContasAberturaOrcamento($rsAberturaOrcamentos, $stFiltro, 'ORDER BY cod_historico', $boTransacao);

//Busca os valores de cada conta para exibir na tela
while (!$rsAberturaOrcamentos->eof()) {
    switch ($rsAberturaOrcamentos->getCampo('cod_estrutural')) {
//Receita Bruta Orçada para o Exercício
        case "5.2.1.1.1.00.00.00.00.00":
            $nuValorReceita = $rsAberturaOrcamentos->getCampo('vl_lancamento');
            $nuValorReceita = str_replace(".", ",", abs($nuValorReceita));
        break;
//Receita Dedutora Bruta Orçada para o Exercício 
    //FUNDEB            
        case "5.2.1.1.2.01.01.00.00.00":
            $nuValorFundeb = $rsAberturaOrcamentos->getCampo('vl_lancamento');
            $nuValorFundeb = str_replace(".", ",", abs($nuValorFundeb));
        break;
    //OUTRAS DEDUCOES
        case "5.2.1.1.2.99.00.00.00.00":
            $nuValorDeducoes = $rsAberturaOrcamentos->getCampo('vl_lancamento');
            $nuValorDeducoes = str_replace(".", ",", abs($nuValorDeducoes));
        break;
    //RENUNCIA
        case "5.2.1.1.2.02.00.00.00.00":
            $nuValorRenuncia = $rsAberturaOrcamentos->getCampo('vl_lancamento');
            $nuValorRenuncia = str_replace(".", ",", abs($nuValorRenuncia));
        break;
//Despesa Prevista para o Exercício
        case "5.2.2.1.1.01.00.00.00.00":
            $nuValorDespesa = $rsAberturaOrcamentos->getCampo('vl_lancamento');
            $nuValorDespesa = str_replace(".", ",", abs($nuValorDespesa));
        break;
    }
    $rsAberturaOrcamentos->proximo();
}

$arContas = array();
$arContas[] = array("nivel" => 1 ,"nom_conta" => "Receita Bruta Orçada para o Exercício", "vl_lancamento" => $nuValorReceita);
$arContas[] = array("nivel" => 1 ,"nom_conta" => "Receita Dedutora Bruta Orçada para o Exercício", "vl_lancamento" => "");
$arContas[] = array("nivel" => 2 ,"nom_conta" => "Fundeb", "vl_lancamento" => $nuValorFundeb);
$arContas[] = array("nivel" => 2 ,"nom_conta" => "Renúncia", "vl_lancamento" => $nuValorRenuncia);
$arContas[] = array("nivel" => 2 ,"nom_conta" => "Outras Deduções", "vl_lancamento" => $nuValorDeducoes);
$arContas[] = array("nivel" => 1 ,"nom_conta" => "Despesa Prevista para o Exercício", "vl_lancamento" => $nuValorDespesa);

$rsContas = new RecordSet;
$rsContas->preenche($arContas);

$obLista = new Lista;
$obLista->setMostraPaginacao( false );
$obLista->setTitulo( "Abertura de Orçamento" );
$obLista->setCampoAgrupado      ( 'nivel' );

$obLista->setRecordSet( $rsContas );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição da Conta" );
$obLista->ultimoCabecalho->setWidth( 90 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Valor" );
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();
$obLista->addCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_conta" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

// Define Objeto Numerico para Valor
$obTxtValor = new Numerico;
$obTxtValor->setName     ( "nuValor_" );
$obTxtValor->setAlign    ( 'RIGHT');
$obTxtValor->setTitle    ( "" );
$obTxtValor->setMaxLength( 19 );
$obTxtValor->setSize     ( 21 );
$obTxtValor->setValue    ( "vl_lancamento" );
$obTxtValor->obEvento->setOnBlur ( "validaValores(this.value,'".Sessao::getId()."'); 
                                    if(this.value < 0){ 
                                        alertaAviso('@Valor Inválido, não pode ser negativo','form','erro','".Sessao::getId()."'); 
                                        jQuery(this).val('0,00'); 
                                    }");

$obLista->addDadoComponente( $obTxtValor );
$obLista->commitDadoComponente();

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName ( "inCodEntidade" );
$obHdnCodEntidade->setValue( $_POST['inCodEntidade']  );

$obHdnCodEstrutural = new Hidden;
$obHdnCodEstrutural->setName ( "stCodEstrutural" );
$obHdnCodEstrutural->setValue( $_POST['stCodEstrutural'] );

//Define o objeto Label Entidade
$obLblCodEntidade = new Label;
$obLblCodEntidade->setRotulo( "Entidade" );
$obLblCodEntidade->setValue( $_POST['inCodEntidade']." - $stNomEntidade" );

//Define o objeto Label Data de Lançamento
$obLblDtLanc = new Label;
$obLblDtLanc->setRotulo( "Data do Lançamento" );
$obLblDtLanc->setValue( '02/01/'.Sessao::getExercicio() );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda('UC-02.02.04');
$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnCtrl              );
$obFormulario->addHidden( $obHdnCodEntidade       );
$obFormulario->addHidden( $obHdnCodEstrutural     );

$obFormulario->addTitulo( "Registros de saldos iniciais" );
$obFormulario->addComponente( $obLblCodEntidade   );
$obFormulario->addComponente( $obLblDtLanc        );

$obFormulario->addLista     ( $obLista            );

$stLocation = $pgFilt.'?'.Sessao::getId().'&stAcao='.$stAcao ;
$obFormulario->Cancelar($stLocation);

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
