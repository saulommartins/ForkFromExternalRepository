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

    * Pacote de configuração do TCMBA - Limites Para Alteração de Créditos Adicionais
    * Data de Criação   : 11/09/2015
    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Evandro Melos
    * 
    * $id:$
    
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';
include_once CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeGeral.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterLimiteAlteracaoCreditoAdicional";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once $pgJS;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdninId = new Hidden;
$obHdninId->setName ( "inId" );
$obHdninId->setId   ( "inId" );

//Define o objeto da span para a listagem
$obSpanLista = new Span;
$obSpanLista->setId   ( "spnLista" );

//Entidades
$obISelectEntidade = new ITextBoxSelectEntidadeGeral();
$obISelectEntidade->setRotulo('*Entidade');

//Norma autorizativa
$obIPopUpNormaAutorizativa = new IPopUpNorma();
$obIPopUpNormaAutorizativa->obInnerNorma->setRotulo          ( "*Nº Lei Autorizativa"     );
$obIPopUpNormaAutorizativa->obInnerNorma->setTitle           ( "*Nº Lei Autorizativa."    );
$obIPopUpNormaAutorizativa->obInnerNorma->obCampoCod->setId  ( "inCodNormaAutorizativa"  );
$obIPopUpNormaAutorizativa->obInnerNorma->obCampoCod->setName( "inCodNormaAutorizativa"  );
$obIPopUpNormaAutorizativa->obInnerNorma->setNull            ( true );

//Hidden para atribuir o valor do campo html
$obHdnDescricaoNorma = new Hidden;
$obHdnDescricaoNorma->setName ( "stDescricaoNorma" );
$obHdnDescricaoNorma->setId   ( "stDescricaoNorma" );

//Tipo de alteracao orcamentaria de acordo com a tabela TCM-BA tcmba.tipo_alteracao_orcamentaria
$obCmbAlteracaoOrcamentaria = new Select();
$obCmbAlteracaoOrcamentaria->setRotulo    ( "*Tipo de alteração orçamentária" );
$obCmbAlteracaoOrcamentaria->setTitle     ( "Informe o tipo de alteração orçamentária de acordo com o TCM-BA." );
$obCmbAlteracaoOrcamentaria->setName      ( "stTipoAlteracaoOrcamentaria" );
$obCmbAlteracaoOrcamentaria->setId        ( "stTipoAlteracaoOrcamentaria" );
$obCmbAlteracaoOrcamentaria->addOption    ( "", "Selecione" );

$obNuValorAlteracao = new Numerico();
$obNuValorAlteracao->setRotulo   ( "Valor da alteração" );
$obNuValorAlteracao->setTitle    ( "Informe o valor da alteração" );
$obNuValorAlteracao->setId       ( "nuValorAlteracao" );
$obNuValorAlteracao->setName     ( "nuValorAlteracao" );
$obNuValorAlteracao->setSize     ( 10 );
$obNuValorAlteracao->setValue    ('0,00');

//Botoes da lista
$obOkLista  = new Ok(false);
$obOkLista->setRotulo('Incluir');
$obOkLista->setValue ('Incluir');
$obOkLista->setId    ('btIncluir');
$obOkLista->setName  ('btIncluir');
$obOkLista->obEvento->setOnClick(" if ( validaCamposLista() ){ montaParametrosGET('incluirLista'); }");
$obLimparLista  = new Button();
$obLimparLista->setId    ('btLimpaLista');
$obLimparLista->setName  ('btLimpaLista');
$obLimparLista->setValue ('Limpar');
$obLimparLista->obEvento->setOnClick(" montaParametrosGET('limparLista'); ");

//Botoes da acao formulario
$obOk  = new Button();
$obOk->setRotulo('Ok');
$obOk->setValue ('Ok');
$obOk->setId    ('Ok');
$obOk->setName  ('Ok');
$obOk->obEvento->setOnClick("BloqueiaFrames(true,false); if (Valida()) { Salvar(); }");
$obLimpar  = new Button();
$obLimpar->setId    ('btLimpar');
$obLimpar->setName  ('btLimpar');
$obLimpar->setValue ('Limpar');
$obLimpar->obEvento->setOnClick(" montaParametrosGET('limparLista'); ");

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addTitulo     ( "Limites Para Alteração de Créditos Adicionais"  );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnDescricaoNorma );
$obFormulario->addHidden     ( $obHdninId );
$obFormulario->addComponente ( $obISelectEntidade );
$obIPopUpNormaAutorizativa->geraFormulario($obFormulario);
$obFormulario->addComponente ( $obCmbAlteracaoOrcamentaria );
$obFormulario->addComponente ( $obNuValorAlteracao );
$obFormulario->defineBarra   ( array( $obOkLista, $obLimparLista ), 'center' );
$obFormulario->addSpan       ( $obSpanLista );
$obFormulario->defineBarra   ( array( $obOk, $obLimpar ) );

$obFormulario->show();

$jsOnLoad = " montaParametrosGET('carregaListaDados'); ";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>