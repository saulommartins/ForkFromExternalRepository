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
  * Página do Oculto da Pop Up
  * Data de Criação: xx/xx/xxxx
  *
  *
  * @author Analista:      Gelson Wolowski Gonçalvez <gelson.goncalves@cnm.org.br>
  * @author Desenvolvedor: Diogo Zarpelon            <diogo.zarpelon@cnm.org.br>
  *

  $Id:$

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once TLIC.'TLicitacaoAta.class.php';

switch ($_REQUEST['stCtrl']) {

    case 'validaNumAta':
        list($inNumAta, $stExercicioAta) = explode('/', $_REQUEST['stNumAta']);

        if (!empty($inNumAta) && !empty($stExercicioAta)) {

            $obTLicitacaoAta = new TLicitacaoAta;
            $obTLicitacaoAta->setCampoCod('');
            $obTLicitacaoAta->setComplementoChave('num_ata, exercicio_ata');

            $obTLicitacaoAta->setDado('num_ata'       , $inNumAta      );
            $obTLicitacaoAta->setDado('exercicio_ata' , $stExercicioAta);
            $obTLicitacaoAta->recuperaPorChave($rsLicitacaoAta);

            # Caso encontre um número de Ata cadastrado, avisa o usuário e limpa o campo.
            if ($rsLicitacaoAta->getNumLinhas() < 0) {
                $stJs .= "alertaAviso('Ata inexistente.', '', 'erro', '".Sessao::getId()."'); \n";
                $stJs .= "jQuery('#".$_REQUEST['stField']."').val('').focus(); \n";
                break;
            }
        }

    break;
}

echo $stJs;

?>
