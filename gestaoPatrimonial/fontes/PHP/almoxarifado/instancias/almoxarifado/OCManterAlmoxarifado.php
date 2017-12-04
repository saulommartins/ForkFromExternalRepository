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
    * Página de Oculto para Almoxarifado
    * Data de Criação: 28/09/2012
    *
    * @author Analista: Gelson
    * @author Desenvolvedor: Eduardo

    * @ignore

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function buscaDadosAlmoxarifado($inCGMAlmoxarifado)
{
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/CGM/classes/negocio/RCGM.class.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/CGM/classes/negocio/RCGMPessoaJuridica.class.php';

    $obRCGM = new RCGM();
    $obRCGMPessoaJuridica = new RCGMPessoaJuridica();

    $obRCGMPessoaJuridica->setNumCGM($inCGMAlmoxarifado);
    $obErro = $obRCGMPessoaJuridica->consultarCGM($rsCGM);

    if (!($obErro->ocorreu())) {
        if (count($rsCGM->arElementos) > 0) {
            $arCGM = $rsCGM->arElementos[0];

            $stEndereco = $arCGM['tipo_logradouro'].' '.$arCGM['logradouro'];

            if (trim($arCGM['numero'])) {
                $stEndereco .= ', '.$arCGM['numero'];
            }

            if (trim($arCGM['complemento'])) {
                $stEndereco .= ', '.$arCGM['complemento'];
            }

            if (trim($arCGM['bairro'])) {
                $stEndereco .= ', '.$arCGM['bairro'];
            }

            $stTelefone = '';

            if (trim($arCGM['fone_residencial']) != '') {
                $stTelefone = $arCGM['fone_residencial'];

                if (trim($arCGM['ramal_residencial']) != '') {
                    $stTelefone .= 'Ramal: '.$arCGM['ramal_residencial'];
                }
            }

            if (trim($arCGM['fone_comercial']) != '') {
                if ($stTelefone != '') {
                    $stTelefone .= ', ';
                }

                $stTelefone .= $arCGM['fone_comercial'];

                if (trim($arCGM['ramal_comercial']) != '') {
                    $stTelefone .= 'Ramal: '.$arCGM['ramal_comercial'];
                }
            }

            if (trim($arCGM['fone_celular']) != '') {
                if ($stTelefone != '') {
                    $stTelefone .= ', ';
                }

                $stTelefone .= $arCGM['fone_celular'];
            }

            $stJs .= "
                if (jq('#inCGMAlmoxarifado').val()) {
                    jq('#stEndereco').html('".$stEndereco."');\n
                    jq('#stTelefone').html('".$stTelefone."');\n
                }
                ";
        } else {
            $stJs .= "
                jq('#stEndereco').html('');\n
                jq('#stTelefone').html('');\n
                ";
        }
    }

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {
    case 'buscaDadosAlmoxarifado':
        if ($_REQUEST['inCGMAlmoxarifado'] != '') {
            $stJs  = buscaDadosAlmoxarifado($_REQUEST['inCGMAlmoxarifado']);
        }
        break;
}

if ($stJs) {
    echo $stJs;
}
?>
