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
 * Filtro para busca das especificações que não possuem contas contábeis
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
$stRecurso = "ContaDestinacao";
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

//Define o objeto TEXT para armazenar o Codigo da especificacao inicial
$obTxtCodEspecificacaoInicial = new TextBox;
$obTxtCodEspecificacaoInicial->setName   ('inCodEspecificacaoInicial');
$obTxtCodEspecificacaoInicial->setRotulo ('Código da Especificação');
$obTxtCodEspecificacaoInicial->setTitle  ('Informe o código da especificação.');
$obTxtCodEspecificacaoInicial->setNull   (true);
$obTxtCodEspecificacaoInicial->setInteiro(true);

// Define Objeto Label
$obLabel = new Label;
$obLabel->setValue(' até ');

//Define o objeto TEXT para armazenar o Codigo da Especificacao inicial
$obTxtCodEspecificacaoFinal = new TextBox;
$obTxtCodEspecificacaoFinal->setName   ('inCodEspecificacaoFinal');
$obTxtCodEspecificacaoFinal->setRotulo ('Código da Especificação');
$obTxtCodEspecificacaoFinal->setTitle  ('Informe o código da especificação.');
$obTxtCodEspecificacaoFinal->setNull   (true);
$obTxtCodEspecificacaoFinal->setInteiro(true);

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm($obForm);

$obFormulario->addTitulo('Dados para Filtro');
$obFormulario->addHidden($obHdnAcao);
$obFormulario->agrupaComponentes(array($obTxtCodEspecificacaoInicial,$obLabel,$obTxtCodEspecificacaoFinal));

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
