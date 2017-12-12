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
 * Filtro para busca dos recursos que não possuem contas contábeis
 *
 * @category   Urbem
 * @package    Framework
 * @author     Analista Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
 * @author     Desenvolvedor Eduardo Schitz <eduardo.schitz@cnm.org.br>
 * $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GF_INCLUDE.'validaGF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stRecurso = "ContaRecurso";
$pgFilt = "FL".$stRecurso.".php";
$pgList = "LS".$stRecurso.".php";
$pgForm = "FM".$stRecurso.".php";
$pgProc = "PR".$stRecurso.".php";
$pgOcul = "OC".$stRecurso.".php";
$pgJS   = "JS".$stRecurso.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = 'incluir';
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgList);
$obForm->setTarget('telaPrincipal'); //oculto - telaPrincipal

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

//Define o objeto TEXT para armazenar o Codigo do Recurso inicial
$obTxtCodRecursoInicial = new TextBox;
$obTxtCodRecursoInicial->setName   ('inCodRecursoInicial');
$obTxtCodRecursoInicial->setRotulo ('Código do Recurso');
$obTxtCodRecursoInicial->setTitle  ('Informe o código do recurso.');
$obTxtCodRecursoInicial->setNull   (true);
$obTxtCodRecursoInicial->setInteiro(true);

// Define Objeto Label
$obLabel = new Label;
$obLabel->setValue(' até ');

//Define o objeto TEXT para armazenar o Codigo do Recurso inicial
$obTxtCodRecursoFinal = new TextBox;
$obTxtCodRecursoFinal->setName   ('inCodRecursoFinal');
$obTxtCodRecursoFinal->setRotulo ('Código do Recurso');
$obTxtCodRecursoFinal->setTitle  ('Informe o código do recurso.');
$obTxtCodRecursoFinal->setNull   (true);
$obTxtCodRecursoFinal->setInteiro(true);

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm($obForm);

$obFormulario->addTitulo('Dados para Filtro');
$obFormulario->addHidden($obHdnAcao);
$obFormulario->agrupaComponentes(array($obTxtCodRecursoInicial,$obLabel,$obTxtCodRecursoFinal));

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
