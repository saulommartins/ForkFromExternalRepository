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
    * Página de Filtro de fornecedor
    * Data de Criação   : 22/09/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    * Casos de uso: uc-03.04.03
*/
/*
$Log$
Revision 1.6  2007/05/30 18:14:26  rodrigo
Bug #9186#

Revision 1.5  2007/02/06 10:20:00  rodrigo
#8202#

Revision 1.4  2007/01/24 10:59:24  bruce
ajustes de interface.

Revision 1.3  2007/01/22 16:51:17  bruce
Bug #7953#

Revision 1.2  2006/09/29 17:34:48  fernando
implementado a alteração do UC-03.04.03

Revision 1.1  2006/09/26 11:05:05  fernando
alterações para o alterar fornecedor

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php");
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );
include_once(CAM_GP_ALM_COMPONENTES."IMontaCatalogoClassificacao.class.php");
include_once(CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php");

$stPrograma = "ManterFornecedor";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include ($pgJs);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "ativar/desativar";
}

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obForm = new Form;
$obForm->setAction                  ( $pgList );

//popup cgm
/*
$obIpopUpCgm = new IPopUpCGM($obForm);
$obIpopUpCgm->setRotulo("Fornecedor");
$obIpopUpCgm->setTitle("Selecione o fornecedor.");
$obIpopUpCgm->setNull (true);
*/

$obIpopUpCgm = new IPopUpCGMVinculado( $obForm                 );
$obIpopUpCgm->setTabelaVinculo       ( 'compras.fornecedor'    );
$obIpopUpCgm->setCampoVinculo        ( 'cgm_fornecedor'        );
$obIpopUpCgm->setNomeVinculo         ( 'Fornecedor'            );
$obIpopUpCgm->setRotulo              ( 'Fornecedor'            );
$obIpopUpCgm->setTitle               ( 'Informe o fornecedor.' );
$obIpopUpCgm->setName                ( 'stNomCGM'              );
$obIpopUpCgm->setId                  ( 'stNomCGM'              );
$obIpopUpCgm->obCampoCod->setName    ( 'inCGM'                 );
$obIpopUpCgm->obCampoCod->setId      ( 'inCGM'                 );
$obIpopUpCgm->obCampoCod->setNull    ( true                    );
$obIpopUpCgm->setNull                ( true                    );

//define rádio de status
$obRdbTodos = new Radio;
$obRdbTodos->setTitle  ( "Selecione o status do fornecedor." );
$obRdbTodos->setName   ( "stStatus" );
$obRdbTodos->setId     ( "stStatus" );
$obRdbTodos->setChecked( true  );
$obRdbTodos->setValue  ( 'T' );
$obRdbTodos->setRotulo ( "Status" );
$obRdbTodos->setLabel  ( "Todos" );
$obRdbTodos->setNull   ( false );

$obRdbAtivo = new Radio;
$obRdbAtivo->setName   ( "stStatus" );
$obRdbAtivo->setId     ( "stStatus" );
$obRdbAtivo->setValue  ( 'A'    );
$obRdbAtivo->setLabel  ( "Ativo"  );

$obRdbInativo = new Radio;
$obRdbInativo->setName   ( "stStatus" );
$obRdbInativo->setId     ( "stStatus" );
$obRdbInativo->setValue  ( 'I'    );
$obRdbInativo->setLabel  ( "Inativo"  );

//define objeto do componente imontacatalogoclassificacao
$obIMontaCatalogoClassificacao = new IMontaCatalogoClassificacao;
$obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->setNull(true);

// seta para que apenas venha catalogos com classificação
$obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->setApenasComClassificacao(true);

$obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->setNaoPermiteManutencao(true);
$obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->obTextBox->setObrigatorioBarra(true);
$obIMontaCatalogoClassificacao->obITextBoxSelectCatalogo->obSelect->setObrigatorioBarra(true);
$obIMontaCatalogoClassificacao->obIMontaClassificacao->setUltimoNivelRequerido(false);
$obIMontaCatalogoClassificacao->obIMontaClassificacao->setClassificacaoRequerida(false);

//define componentes para  atividades
$obMontaAtividade = new MontaAtividade();
$obMontaAtividade->obRCEMAtividade->recuperaVigenciaAtual( $rsVigenciaAtual );
$obMontaAtividade->setCodigoVigencia( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );
$obMontaAtividade->setCadastroAtividade(false);
$obMontaAtividade->setNullBarra(true);

$obFormulario = new Formulario;
$obFormulario->addTitulo                 ( "Dados para o Filtro" );
$obFormulario->addForm                   ( $obForm               );
$obFormulario->setAjuda                  ("UC-03.04.03");
$obFormulario->addHidden                 ( $obHdnAcao            );
$obFormulario->addHidden                 ( $obHdnCtrl            );
$obFormulario->addComponente             ($obIpopUpCgm);

if ( $stAcao == 'ativar/desativar' )
    $obFormulario->agrupaComponentes         ( array( $obRdbTodos, $obRdbAtivo ,$obRdbInativo ) );
$obFormulario->addTitulo                 ( "Dados da Classificação" );
$obIMontaCatalogoClassificacao->geraFormulario($obFormulario);
$obFormulario->addTitulo        ( "Ramos de Atividade"   );
$obMontaAtividade->geraFormulario($obFormulario);
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
