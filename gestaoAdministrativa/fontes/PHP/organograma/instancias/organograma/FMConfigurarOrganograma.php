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
    * Página de Formulário para Configurar o Organograma
    * Data de criação : 10/12/2008

    * @author Analista: Gelson Wolowski
    * @author Programador: Diogo Zarpelon

    * @ignore

    $Id:$

    **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrganograma.class.php");

$stPrograma = "ConfigurarOrganograma";
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

$obRegraOrganograma = new ROrganogramaOrganograma;

# Filtra para listar somente organogramas com data menor ou igual ao dia.
$stFiltro = " WHERE implantacao <= '".date('Y-m-d')."'";

$obRegraOrganograma->listarOrganogramas($rsOrganograma, '', '', $stFiltro);

while (!$rsOrganograma->eof()) {
    # Teste para saber qual organograma deve vir selecionado ao carregar a tela.
    if ($rsOrganograma->getCampo('ativo') == 't') {
        $inCodOrganograma = $rsOrganograma->getCampo('cod_organograma');
    }

    $rsOrganograma->proximo();
}

$rsOrganograma->setPrimeiroElemento();

# Cria o objeto Select que irá listar todos os Organogramas possíveis de serem ativos.
$obCmbOrganograma = new Select;
$obCmbOrganograma->setRotulo    ('Organograma Ativo');
$obCmbOrganograma->setName      ('inCodOrganograma');
$obCmbOrganograma->setId        ('inCodOrganograma');
$obCmbOrganograma->setCampoId   ('[cod_organograma]');
$obCmbOrganograma->setCampoDesc ('[cod_organograma] - [implantacao]');
$obCmbOrganograma->preencheCombo($rsOrganograma);
$obCmbOrganograma->setValue     ($inCodOrganograma);

# Define o botão para submeter o formulário.
$obButtonSubmit = new Button;
$obButtonSubmit->setName  ('ok');
$obButtonSubmit->setId    ('ok');
$obButtonSubmit->setValue ('Ok');
$obButtonSubmit->obEvento->setOnClick('document.frm.submit();');

$obFormulario->addTitulo    ('Ativar Organograma'  );
$obFormulario->addComponente($obCmbOrganograma     );
$obFormulario->defineBarra  (array($obButtonSubmit));

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
