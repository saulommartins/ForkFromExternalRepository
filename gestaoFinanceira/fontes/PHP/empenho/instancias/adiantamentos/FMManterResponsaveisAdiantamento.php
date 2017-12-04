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
/*
    * Página de Formulário de responsáveis por adiantamento
    * Data de Criação   : 13/11/2006

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Rodrigo

    * @ignore

    * Casos de uso : uc-02.03.32
*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include Componentes
include_once CAM_GF_CONT_COMPONENTES.'IPopUpContaAnalitica.class.php';
include_once CAM_GA_CGM_COMPONENTES.'IPopUpCGM.class.php';

$stCtrl = $_POST["stCtrl"] ? $_POST["stCtrl"] : $_GET["stCtrl"];
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

($stAcao=="")?$stAcao="incluir":$stAcao=$stAcao;

Sessao::remove('arValores');

$stPrograma = "ManterResponsaveisAdiantamento";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

$stLocation = $pgList."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'];

//Definição do Form
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//Define o objeto de controle
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

//Define o código do responsavel
$obHdnCodResponsavel = new Hidden;
$obHdnCodResponsavel->setName  ( "HdnCodResponsavel" );
$obHdnCodResponsavel->setValue ( ""                  );

//Define o nome do responsavel
$obHdnNomResponsavel = new Hidden;
$obHdnNomResponsavel->setName  ( "HdnNomResponsavel" );
$obHdnNomResponsavel->setValue ( ""                  );

//Campo prazo máximo para prestação de contas
$obNroPrazo = new TextBox;
$obNroPrazo->setName      ( "inPrazo"                                                            );
$obNroPrazo->setRotulo    ( "Prazo máximo p/ Prestação de Contas"                                );
$obNroPrazo->setTitle     ( "Informe o prazo máximo para prestação de contas de um adiantamento" );
$obNroPrazo->setNull      ( false                                                                );
$obNroPrazo->setMaxLength ( 3                                                                    );
$obNroPrazo->setSize      ( 5                                                                    );
$obNroPrazo->setInteiro   ( true                                                                 );

// Label para adicionar a palavra dias após o campo "Prazo máximo p/ Prestação de Contas"
$obLblPrazo = new Label;
$obLblPrazo->setRotulo    ( "Prazo máximo p/ Prestação de Contas"                                );
$obLblPrazo->setValue('dias');

//Campo Contrapartida Contábil
$obPopUpContraPartida = new IPopUpContaAnalitica ( $obEntidadeUsuario->obSelect                                       );
$obPopUpContraPartida->setID                     ( 'innerContraPartida'                                               );
$obPopUpContraPartida->setName                   ( 'innerContraPartida'                                               );
$obPopUpContraPartida->obCampoCod->setName       ( "inCodContraPartida"                                               );
$obPopUpContraPartida->setRotulo                 ( 'Contrapartida Contábil'                                           );
$obPopUpContraPartida->setTitle                  ( 'Informe o código da conta do Passivo Compensado'                  );
$obPopUpContraPartida->setTipoBusca              ( 'plano_contas_PCASP'                                               );
$obPopUpContraPartida->setNull                   ( false                                                              );
$obPopUpContraPartida->obCampoCod->obEvento->setOnBlur( "montaParametrosGET('carregaResponsavelAdiantamento');" );
$obPopUpContraPartida->obImagem->setId('btContraPartida');

//Campo Credor
$obPopUpCredor = new IPopUpCGM( $obForm );
$obPopUpCredor->setNull     ( true               );
$obPopUpCredor->setRotulo   ( "*Credor"          );
$obPopUpCredor->setTitle    ( "Informe o credor" );
$obPopUpCredor->setTipo     ( "fisica"           );
$obPopUpCredor->obImagem->setId('btCredor');

//Campo Conta Lançamento
$obPopUpContaLancamento = new IPopUpContaAnalitica ( $obEntidadeUsuario->obSelect                                );
$obPopUpContaLancamento->setID                     ( 'innerContaLancamento'                                      );
$obPopUpContaLancamento->setName                   ( 'innerContaLancamento'                                      );
$obPopUpContaLancamento->obCampoCod->setName       ( "inCodContaLancamento"                                      );
$obPopUpContaLancamento->setRotulo                 ( '*Conta Contábil'                                           );
$obPopUpContaLancamento->setTitle                  ( 'Informe o código da conta do Ativo Compensado'             );
$obPopUpContaLancamento->setTipoBusca              ( 'emp_conta_lancamento_adiantamentos'                        );
$obPopUpContaLancamento->obImagem->setId('btContaLancamento');

//Campo de Situação com o valor SIM
$obRadSituacaoAtivo = new Radio;
$obRadSituacaoAtivo->setName   ('inCodSituacao');
$obRadSituacaoAtivo->setRotulo ('Situação'     );
$obRadSituacaoAtivo->setLabel  ('Ativo'        );
$obRadSituacaoAtivo->setValue  ('A'            );
$obRadSituacaoAtivo->setChecked(true           );
$obRadSituacaoAtivo->setNull   (false          );
$obRadSituacaoAtivo->setId     ('SituacaoS'    );

//Campo de Situação com valor NÃO
$obRadSituacaoInativo = new Radio;
$obRadSituacaoInativo->setName   ('inCodSituacao');
$obRadSituacaoInativo->setRotulo ('Situação'     );
$obRadSituacaoInativo->setLabel  ('Inativo'      );
$obRadSituacaoInativo->setValue  ('I'            );
$obRadSituacaoInativo->setChecked(false          );
$obRadSituacaoInativo->setNull   (false          );
$obRadSituacaoInativo->setId     ('SituacaoN'    );

//Define Objeto Button para Incluir Responsáveis por adiantamentos
$obBtnIncluirResponsaveis = new Button;
$obBtnIncluirResponsaveis->setValue            ("Incluir"                                        );
$obBtnIncluirResponsaveis->setId               ("incluiResponsavel"                              );
$obBtnIncluirResponsaveis->obEvento->setOnClick("montaParametrosGET('incluirListaResponsaveis');");
//$obBtnIncluirResponsaveis->obEvento->setOnClick("incluiResponsaveis('incluirListaResponsaveis');");

//Define Objeto Button para Limpar Responsáveis por adiantamentos
$obBtnLimparResponsaveis = new Button;
$obBtnLimparResponsaveis->setValue             ( "Limpar"               );
$obBtnLimparResponsaveis->obEvento->setOnClick ( "limparResponsaveis()" );

//Span da Listagem de veículos de Publicação Utilizados
$obSpnListaResponsaveis = new Span;
$obSpnListaResponsaveis->setID("spnListaResponsaveis");

$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                                                      );
$obFormulario->addHidden        ( $obHdnAcao                                                   );
$obFormulario->addHidden        ( $obHdnCtrl                                                   );
$obFormulario->addHidden        ( $obHdnCodResponsavel                                         );
$obFormulario->addHidden        ( $obHdnNomResponsavel                                         );
$obFormulario->addTitulo        ( "Dados para cadastro de Contrapartida"                       );
$obFormulario->addComponente    ( $obPopUpContraPartida                                        );
$obFormulario->agrupaComponentes( array( $obNroPrazo , $obLblPrazo )                           );
$obFormulario->addTitulo        ( "Dados para cadastro de Responsáveis por Adiantamentos"      );
$obFormulario->addComponente    ( $obPopUpCredor                                               );
$obFormulario->addComponente    ( $obPopUpContaLancamento                                      );
$obFormulario->agrupaComponentes( array( $obRadSituacaoAtivo , $obRadSituacaoInativo )         );
$obFormulario->agrupaComponentes( array( $obBtnIncluirResponsaveis, $obBtnLimparResponsaveis ) );
$obFormulario->addSpan          ( $obSpnListaResponsaveis                                      );
if($stAcao=='incluir')
    $obFormulario->OK();
else
    $obFormulario->Cancelar( $stLocation );
$obFormulario->show();

if ($stAcao=="alterar") {
  $link = "&inCodContraPartida=".$_GET['inCodContraPartida'];
  $stJs="ajaxJavaScript('".$pgOcul."?".Sessao::getId().$link."','carregaResponsavelAdiantamento')";
} else {
  $stJs="ajaxJavaScript('".$pgOcul."?".Sessao::getId()."','carregaListaResponsaveis');";
}
$jsOnLoad = $stJs;
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
