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
    * Página de Formulário para Migrar Organograma - Local
    * Data de criação : 08/12/2008

    * @author Analista: Gelson Wolowski
    * @author Programador: Diogo Zarpelon

    * @ignore

    $Id:$

    **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "MigraOrganogramaLocal";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

# Definição dos Componentes
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( 'oculto' );

# Definição do Formulário
$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );

# Span que irá guardar a tabela de Migração.
$obSpan = new Span;
$obSpan->setId('spnTable');

# Define o botão para submeter o formulário.
$obButtonSubmit = new Button;
$obButtonSubmit->setRotulo('Salvar');
$obButtonSubmit->setName  ('ok');
$obButtonSubmit->setId    ('ok');
$obButtonSubmit->setValue ('Ok');
$obButtonSubmit->obEvento->setOnClick('document.frm.submit();');

$obFormulario->addSpan($obSpan);
$obFormulario->addComponente ($obButtonSubmit);

$obFormulario->show();

# Faz a requisição para montar a tabela da migração.
$stJs  = "<script>\n";
$stJs .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."','montaTabelaMigracao');";
$stJs .= "</script>\n";

echo $stJs;

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
