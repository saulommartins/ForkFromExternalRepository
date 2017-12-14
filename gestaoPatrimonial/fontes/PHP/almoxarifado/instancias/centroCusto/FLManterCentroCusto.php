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
    * Página de Formulário Almoxarifado
    * Data de Criação   : 22/11/2005

    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis

    * @ignore

    * Casos de uso: uc-03.03.07
*/

/*
$Log$
Revision 1.10  2006/07/20 21:11:46  fernando
alteração na padronização dos UC

Revision 1.9  2006/07/19 11:43:57  fernando
Inclusão do  Ajuda.

Revision 1.8  2006/07/17 20:32:56  fernando
alteração de hint

Revision 1.7  2006/07/06 14:00:30  diego
Retirada tag de log com erro.

Revision 1.6  2006/07/06 12:09:52  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoCentroDeCustos.class.php");

$stPrograma = "ManterCentroCusto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRegra = new RAlmoxarifadoCentroDeCustos();
$obRegra->roUltimaEntidade->setExercicio ( Sessao::getExercicio() );
$obRegra->roUltimaEntidade->listar( $rsEntidade );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obForm = new Form;
$obForm->setAction                  ( $pgList );
// Define SELECT multiplo para codigo da entidade
$obCmbEntidade = new SelectMultiplo();
$obCmbEntidade->setName       ( 'inCodEntidade'          );
$obCmbEntidade->setRotulo     ( "Entidades"              );
$obCmbEntidade->setTitle      ( "Selecione as entidades.");
// lista de atributos disponiveis
$obCmbEntidade->SetNomeLista1 ('inCodEntidadeDisponivel' );
$obCmbEntidade->setCampoId1   ( 'cod_entidade'           );
$obCmbEntidade->setCampoDesc1 ( 'nom_cgm'                );
$obCmbEntidade->SetRecord1    ( $rsEntidade              );
// lista de atributos selecionados
$obCmbEntidade->SetNomeLista2 ( 'inCodEntidade'          );
$obCmbEntidade->setCampoId2   ( 'cod_entidade'           );
$obCmbEntidade->setCampoDesc2 ( 'nom_cgm'                );
$obCmbEntidade->SetRecord2    ( new Recordset()          );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo ( "Descrição"  );
$obTxtDescricao->setName   ( "stDescricao" );
$obTxtDescricao->setSize ( 50 );
$obTxtDescricao->setMaxLength ( 160 );
$obTxtDescricao->setTitle ( "Informe a descrição do centro de custo" );
$obTxtDescricao->setValue  ( $stDescricao );

$obCmpTipoBusca = new TipoBusca( $obTxtDescricao );

$obRdbOrdCod = new Radio;
$obRdbOrdCod->setName    ( 'stOrder');
$obRdbOrdCod->setRotulo  ( 'Ordenar por' );
$obRdbOrdCod->setTitle   ( 'Escolha por qual campo ordenar a pesquisa.' );
$obRdbOrdCod->setLabel   ( 'Código'  );
$obRdbOrdCod->setValue   ( 'ORDER BY centro_custo.cod_centro' );
$obRdbOrdCod->setChecked (  false );

$obRdbOrdDesc = new Radio;
$obRdbOrdDesc->setName    ( 'stOrder'  );
$obRdbOrdDesc->setLabel   ( 'Descrição');
$obRdbOrdDesc->setValue   ( 'ORDER BY centro_custo.descricao' );
$obRdbOrdDesc->setChecked ( true );

$obRdbOrdResponsavel = new Radio;
$obRdbOrdResponsavel->setName    ( 'stOrder'  );
$obRdbOrdResponsavel->setLabel   ( 'Responsável');
$obRdbOrdResponsavel->setValue   ( 'ORDER BY sw_cgm.nom_cgm' );
$obRdbOrdResponsavel->setChecked ( false );

$obFormulario = new Formulario;
$obFormulario->addTitulo                 ( "Dados Para o Filtro" );
$obFormulario->addForm                   ( $obForm               );
$obFormulario->setAjuda                  ("UC-03.03.07");
$obFormulario->addHidden                 ( $obHdnAcao            );
$obFormulario->addComponente             ( $obCmbEntidade        );
$obFormulario->addComponente             ( $obCmpTipoBusca       );
$obFormulario->agrupaComponentes(array($obRdbOrdCod, $obRdbOrdDesc, $obRdbOrdResponsavel));

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
