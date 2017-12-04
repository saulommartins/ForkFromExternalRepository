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
/**
 * Página de Regra Notas Fiscais
 * Data de Criação : 13/07/2015
 * @author Analista: Luciana Dellay
 * @author Desenvolvedor: Evandro Melos
 * $Id: $
 * $Name: $
 * $Revision: $
 * $Author: $
 * $Date: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';


class RAlmoxarifadoNotaFiscal
{

    public function __construct()
    {
        
    }

    public function buscarAlmoxarifadosDisponiveis(&$rsDisponiveisAlmox,&$rsPermitidosAlmox)
    {
        include_once CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoAlmoxarife.class.php";
        $obRAlmoxarifadoAlmoxarife = new RAlmoxarifadoAlmoxarife;

        $obRAlmoxarifadoAlmoxarife->listarDisponiveis( $rsDisponiveisAlmox , "codigo" );
        $obRAlmoxarifadoAlmoxarife->listarPadrao ( $rsPermitidosAlmox      , "codigo" );

        while ( !$rsDisponiveisAlmox->eof() ) {
            $stNomeAlmoxarifados[$rsDisponiveisAlmox->getCampo('codigo')] = $rsDisponiveisAlmox->getCampo( 'nom_a');
            foreach ($rsPermitidosAlmox->arElementos as $key => $valor) {
                if ( $valor['nom_a'] == $rsDisponiveisAlmox->getCampo('nom_a') ) {
                    unset($rsDisponiveisAlmox->arElementos[$rsDisponiveisAlmox->getCorrente()-1]);
                }
            }
            $rsDisponiveisAlmox->proximo();
        }

        Sessao::write('stNomeAlmoxarifados', $stNomeAlmoxarifados);

        $arDiff = array();
        if (is_array($rsDisponiveisAlmox->arElementos)&&is_array($rsPermitidosAlmox->arElementos)) {
          $arDiff = array_diff_assoc($rsDisponiveisAlmox->arElementos, $rsPermitidosAlmox->arElementos );
        }

        $arTmp = array();
        foreach ($arDiff as $Valor) {
            array_push($arTmp,$Valor);
        }
        $rsDisponiveisAlmox = new RecordSet;
        $rsDisponiveisAlmox->preenche($arTmp);

        return true;
    }

}//Fim da Classe