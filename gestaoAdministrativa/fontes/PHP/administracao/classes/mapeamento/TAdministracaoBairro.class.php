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
* Classe de mapeamento para administracao.bairro
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.03.98
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TBairro extends Persistente
{
function TBairro()
{
    parent::Persistente();
    $this->setTabela('sw_bairro');
    $this->setCampoCod('cod_bairro');
    $this->setComplementoChave('cod_municipio, cod_uf');

    $this->AddCampo('cod_bairro',    'integer', true, '', true,  false);
    $this->AddCampo('cod_municipio', 'integer', true, '', true,  true);
    $this->AddCampo('cod_uf',        'integer', true, '', true,  true);
    $this->AddCampo('nom_bairro',    'varchar', true, 30, false, false);
}

function montaRecuperaRelacionamento()
{
    $stSql  = "";
    $stSql .= "  SELECT                                     \n";
    $stSql .= "     B.*,                                    \n";
    $stSql .= "     U.nom_uf,                               \n";
    $stSql .= "     M.nom_municipio                         \n";
    $stSql .= "  FROM                                       \n";
    $stSql .= "     sw_bairro     AS B,                    \n";
    $stSql .= "     sw_uf         AS U,                    \n";
    $stSql .= "     sw_municipio  AS M                     \n";
    $stSql .= "  WHERE                                      \n";
    $stSql .= "     B.cod_uf        = U.cod_uf        AND   \n";
    $stSql .= "     B.cod_municipio = M.cod_municipio AND   \n";
    $stSql .= "     M.cod_uf        = U.cod_uf              \n";

    return $stSql;
}
}
