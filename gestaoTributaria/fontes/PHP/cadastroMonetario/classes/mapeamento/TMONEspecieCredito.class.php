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
    * Classe de mapeamento para MONETARIO.ESPECIE_CREDITO
    * Data de Criação: 20/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @package URBEM
    * @subpackage Regra

    * $Id: TMONEspecieCredito.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.09
*/

/*
$Log$
Revision 1.8  2006/09/15 14:46:11  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

/*include_once    ("../../../includes/Constante.inc.php");*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TMONEspecieCredito extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TMONEspecieCredito()
{
    parent::Persistente();
    $this->setTabela('monetario.especie_credito');

    $this->setCampoCod('cod_especie');
    $this->setComplementoChave('cod_especie,cod_genero,cod_natureza');

    $this->AddCampo('cod_especie',  'integer',true,'',  true,false);
    $this->AddCampo('cod_genero',   'integer',true,'',  true,true);
    $this->AddCampo('cod_natureza', 'integer',true,'',  true,true);
    $this->AddCampo('nom_especie',  'varchar',true,'80',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT                           \n";
    $stSql .= " e.*,                            \n";
    $stSql .= " n.nom_natureza,                 \n";
    $stSql .= " g.nom_genero                    \n";
    $stSql .= "FROM                             \n";
    $stSql .= " monetario.especie_credito e     \n";
    $stSql .= "INNER JOIN                       \n";
    $stSql .= " monetario.genero_credito g      \n";
    $stSql .= "ON                               \n";
    $stSql .= " e.cod_genero = g.cod_genero     \n";
    $stSql .= "AND                              \n";
    $stSql .= " e.cod_natureza = g.cod_natureza \n";
    $stSql .= "INNER JOIN                       \n";
    $stSql .= " monetario.natureza_credito n    \n";
    $stSql .= "ON                               \n";
    $stSql .= " n.cod_natureza = g.cod_natureza \n";

 return $stSql;
}

}
