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
    * Página de Filtro de Mapa de Compras
    * Data de Criação   :06/10/2006

    * @author Analista: Cleisson Barbosa
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    * Casos de uso:uc-06.04.00
*/

/**
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterObras";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction                  ( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obNumero = new Inteiro();
$obNumero->setRotulo('Número da Obra');
$obNumero->setName  ('inNumero');
$obNumero->setId    ('inNumero');
$obNumero->setValue ($inNumero);
$obNumero->setMaxLength( 4 );
$obNumero->setSize  ( 5 );

$obLocalidade = new TextBox();
$obLocalidade->setRotulo('Localidade');
$obLocalidade->setName  ('stLocalidade');
$obLocalidade->setId    ('stLocalidade');
$obLocalidade->setValue ($stLocalidade);
$obLocalidade->setMaxLength( 150 );
$obLocalidade->setSize  ( 50 );

$obDescricao = new TextArea;
$obDescricao->setRotulo ( "Descrição" );
$obDescricao->setName   ( "stDescricao" );
$obDescricao->setId     ( "stDescricao" );
$obDescricao->setValue  ( $stDescricao  );
$obDescricao->setRows   ( 3 );

$obFormulario = new Formulario();
$obFormulario->addForm              ($obForm);

$obFormulario->addHidden     ($obHdnAcao      );
$obFormulario->addHidden     ($obHdnCtrl      );
$obFormulario->addComponente ($obNumero       );
$obFormulario->addComponente ($obLocalidade   );
$obFormulario->addComponente ($obDescricao    );

$obFormulario->ok();
$obFormulario->show();

?>
