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
    * PÃ¡gina de Listagem de Itens
    * Data de CriaÃ§Ã£o   : 08/09/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 31732 $
    $Name$
    $Autor:$
    $Date: 2007-08-13 15:55:06 -0300 (Seg, 13 Ago 2007) $

    * Casos de uso: uc-02.04.03

*/

/*
$Log$
Revision 1.15  2007/08/13 18:48:35  vitor
Ajustes em: Tesouraria :: Configuração :: Classificar Receitas

Revision 1.14  2007/07/06 13:59:32  vitor
Bug#9131#

Revision 1.13  2007/07/04 14:11:42  vitor
Bug#9404#

Revision 1.12  2007/06/14 14:26:55  domluc
Ajuste na Ordenação

Revision 1.11  2007/05/29 14:11:35  domluc
Mudanças na forma de classificação de receitas.

Revision 1.10  2007/03/15 19:03:43  domluc
Caso de Uso 02.04.33

Revision 1.9  2007/03/09 15:41:51  domluc
uc-02.04.33

Revision 1.8  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterReceitas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

// Define ação padrão do arquivo
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$arFiltro = Sessao::read('filtro');
if ( !count( $arFiltro ) > 0 ) {
    $arFiltro = $_REQUEST;
    Sessao::write('filtro', $arFiltro);
} else {
    $_REQUEST = $arFiltro;
}

if ($_REQUEST['stTipoReceita'] != 'orcamentaria') {  // caso seja extra-orcamentaria
    $stTituloTable = "Receitas Extra-Orçamentárias";

    if ($_REQUEST['stContaInicial'] && !$_REQUEST['stContaFinal']) {
        $_REQUEST['stContaFinal'] = $_REQUEST['stContaInicial'];
    }
    if ($_REQUEST['stContaFinal'] && !$_REQUEST['stContaInicial']) {
        $_REQUEST['stContaInicial'] = $_REQUEST['stContaFinal'];
    }

    require_once (CAM_GF_CONT_MAPEAMENTO . 'TContabilidadePlanoConta.class.php');
    $obTPlanoConta = new TContabilidadePlanoConta();
    $obTPlanoConta->setDado('plano_inicial' , $_REQUEST['stContaInicial']);
    $obTPlanoConta->setDado('plano_final' , $_REQUEST['stContaFinal']);
    $obTPlanoConta->setDado('exercicio' , Sessao::getExercicio());

    $stOrdem  = "  group by 1,2,3,4,5,6,7 order by plano_conta.cod_estrutural, possui_creditos desc, nom_conta asc";

    $obTPlanoConta->recuperaClassReceitasExtraOrcamentariasCredito( $rsReceitas, '', $stOrdem);

} else { // caso seja orcamentaria
    $stTituloTable = "Receitas Orçamentárias";
    if ($_REQUEST['stCodEstruturalInicial'] && !$_REQUEST['stCodEstruturalFinal']) {
        $_REQUEST['stCodEstruturalFinal'] = $_REQUEST['stCodEstruturalInicial'];
    }
    if ($_REQUEST['stCodEstruturalFinal'] && !$_REQUEST['stCodEstruturalInicial']) {
        $_REQUEST['stCodEstruturalInicial'] = $_REQUEST['stCodEstruturalFinal'];
    }

    require_once (CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoReceita.class.php');
    $obTReceita = new TOrcamentoReceita();
    $obTReceita->setDado('cod_estrutural_inicial' , $_REQUEST['stCodEstruturalInicial']);
    $obTReceita->setDado('cod_estrutural_final' , $_REQUEST['stCodEstruturalFinal']);
    $obTReceita->setDado('inCodReceitaInicial' , $_REQUEST['inCodReceitaInicial']);
    $obTReceita->setDado('inCodReceitaFinal' , $_REQUEST['inCodReceitaFinal']);

    $obTReceita->setDado('exercicio' , Sessao::getExercicio());

    $stOrdem  = " conta_receita.cod_estrutural, possui_creditos desc, \"desc\" asc";
    $obTReceita->recuperaClassReceitasOrcamentariasCredito( $rsReceitas, '', $stOrdem);
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

$table = new TableTree();
$table->setRecordset( $rsReceitas );
$table->setArquivo( 'OCManterReceitas.php');
$table->setParametros( array( "codigo" , "cod_estrutural") );
$table->setComplementoParametros( "stCtrl=creditos_receita&tipo_receita=".$_REQUEST['stTipoReceita']);

// Defina o título da tabela
$table->setSummary( $stTituloTable );
$table->addCondicionalTree( 'possui_creditos' , 't' );

// lista zebrada
//$table->setConditional( true , "#efefef" );

$table->Head->addCabecalho( 'Código Reduzido' , 10  );
$table->Head->addCabecalho( 'Código Estrutural' , 10  );
$table->Head->addCabecalho( 'Descrição' , 60  );

$table->Body->addCampo( 'codigo', 'C' );
$table->Body->addCampo( 'cod_estrutural', 'C' );
$table->Body->addCampo( 'desc', 'E' );

$table->Body->addAcao(  'alterar' ,
                        'detalhaConta(\'%s\' ,\'%s\', \'%s\' , \'%s\', \'%s\', \'%s\')'
                        , array( 'exercicio' ,
                                 'cod_entidade',
                                 'codigo'	,
                                 'cod_estrutural',
                                 'desc',
                                $_REQUEST['stTipoReceita'] )
                        , 'npossui_arrecadacao' );

$table->montaHTML();
echo $table->getHTML();

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnRedireciona = new Hidden;
$obHdnRedireciona->setName( "stRedireciona" );
$obHdnRedireciona->setValue( "" );

$obHdnEval = new HiddenEval;
$obHdnEval->setName( "hdnEval" );
$obHdnEval->setValue( "BloqueiaFrames(true,false);" );

// Define objeto Span para
$obSpnListaOrcamentaria = new Span();
$obSpnListaOrcamentaria->setId( 'spnListaOrcamentaria' );

// Define objeto Span para
$obSpnListaExtra = new Span();
$obSpnListaExtra->setId( 'spnListaExtra' );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm            ( $obForm            );
$obFormulario->addHidden          ( $obHdnAcao         );
$obFormulario->addHidden          ( $obHdnCtrl         );
$obFormulario->addHidden          ( $obHdnRedireciona  );
$obFormulario->addHidden          ( $obHdnEval, true   );
/*
if ($_REQUEST['stTipoReceita'] != 'extra') {
    $obFormulario->addTitulo          ( "Receitas Orçamentárias" );
    $obFormulario->addSpan            ( $obSpnListaOrcamentaria  );
}

if ($_REQUEST['stTipoReceita'] != 'orcamentaria') {
    $obFormulario->addTitulo          ( "Receitas Extra-Orçamentárias" );
    $obFormulario->addSpan            ( $obSpnListaExtra         );
}
*/
$obFormulario->Cancelar( $pgFilt.'?'.Sessao::getId());
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
