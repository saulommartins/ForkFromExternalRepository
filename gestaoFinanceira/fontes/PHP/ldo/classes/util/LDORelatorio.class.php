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
 * Classe Relatorio Util
 * Data de Criação: 18/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Janilson Mendes Pereira da Silva <janilson.silva>
 * @package GF
 * @subpackage LDO
 */
include_once CAM_GF_ORC_MAPEAMENTO . "TOrcamentoEntidade.class.php" ;

class LDORelatorio
{
    /**
     * Monta as todas Entidades cadastradas no Sistema
     * por exercicio vigente
     * @return String
     */
    public static function montarEntidades()
    {
        $obTOrcamentoEntidade = new TOrcamentoEntidade();

        $obTOrcamentoEntidade->setDado("exercicio", Sessao::read("exercicio"));
        $obTOrcamentoEntidade->recuperaEntidadeGeral($obRecordSet, "", "");

        $inCount = count($obRecordSet->arElementos);

        $arEntidades = array();

        for ($i = 0; $i < $inCount; $i++) {
            $arCampos        = $obRecordSet->arElementos[$i];
            $arEntidades[$i] = $arCampos['cod_entidade'];
        }

        asort($arEntidades);

        return implode(',', $arEntidades);
    }
}
