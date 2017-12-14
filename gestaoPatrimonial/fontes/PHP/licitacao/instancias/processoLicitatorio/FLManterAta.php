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
    * Página de Filtro do cadastro de Ata
    * Data de Criação: 14/01/2009

    * @author Analista: Gelson
    * @author Desenvolvedor: Diogo Zarpelon

    * @ignore

    $Id:$

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_LIC_COMPONENTES."IPopUpNumeroAta.class.php";
include_once CAM_GP_LIC_COMPONENTES."IPopUpNumeroEdital.class.php";

//Define o nome dos arquivos PHP
$stPrograma   = "ManterAta";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js" ;

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction ( $pgList  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ('stCtrl');
$obHdnCtrl->setValue ($stCtrl);

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ('stAcao');
$obHdnAcao->setValue ($stAcao);

$obAta = new IPopUpNumeroAta($obForm);
$obAta->obCampoCod->setId   ('stNumAta');
$obAta->obCampoCod->setName ('stNumAta');

$stFiltroBuscaEditais = " AND (                                                                                                             \n
                                EXISTS  (                                                                                                   \n
                                          SELECT 1                                                                                          \n
                                            FROM compras.julgamento                                                                         \n
                                      INNER JOIN compras.mapa_cotacao                                                                       \n
                                              ON julgamento.exercicio = mapa_cotacao.exercicio_cotacao                                      \n
                                             AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao                                          \n

                                           WHERE ll.cod_licitacao = le.cod_licitacao                                                        \n
                                             AND ll.cod_modalidade = le.cod_modalidade                                                      \n
                                             AND ll.cod_entidade = le.cod_entidade                                                          \n
                                             AND ll.exercicio = le.exercicio                                                                \n
                                             AND ll.exercicio_mapa = mapa_cotacao.exercicio_mapa                                            \n
                                             AND ll.cod_mapa = mapa_cotacao.cod_mapa                                                        \n
                                             AND NOT EXISTS (                                                                               \n
                                                               SELECT 1                                                                     \n
                                                                 FROM compras.cotacao_anulada                                               \n
                                                                WHERE cotacao_anulada.cod_cotacao = mapa_cotacao.cod_cotacao                \n
                                                                  AND cotacao_anulada.exercicio = mapa_cotacao.exercicio_cotacao            \n
                                                            )                                                                               \n
                                        )                                                                                                   \n
                            )";

$obEdital = new IPopUpNumeroEdital($obForm);
$obEdital->obCampoCod->setId   ('stNumEdital');
$obEdital->obCampoCod->setName ('stNumEdital');
$obEdital->setFiltroEditaisAdicional($stFiltroBuscaEditais);

$obFormulario = new Formulario;

$obFormulario->addForm($obForm);

$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);

# Instancia dos componentes de pesquisa.
$obFormulario->addComponente($obAta);
$obFormulario->addComponente($obEdital);

$obFormulario->Ok();
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
