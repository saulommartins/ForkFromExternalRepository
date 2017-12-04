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
    * Classe de regra de negócio para MONETARIO.CREDITO_INDICADOR
    * Data de Criação: 22/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage Regra

    * $Id: TMONCreditoIndicador.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.10
*/

/*
$Log$
Revision 1.7  2006/09/15 14:46:11  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TMONCreditoIndicador extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TMONCreditoIndicador()
{
   parent::Persistente();
   $this->setTabela('monetario.credito_indicador');

   $this->setCampoCod('');
   $this->setComplementoChave('cod_credito');

   $this->AddCampo('cod_credito','INTEGER',true,'',true,false);
   $this->AddCampo('cod_natureza','INTEGER',true,'',true,true);
   $this->AddCampo('cod_genero','INTEGER',true,'',true,true);
   $this->AddCampo('cod_especie','INTEGER',true,'',true,true);
   $this->AddCampo('cod_indicador','INTEGER',true,'',true, true);
   $this->AddCampo('timestamp','timestamp',false,'',false,true);

}

function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT                                                                                                                                        \r\n ";
    $stSql .= "     mc.cod_credito,                                                                                                                       \r\n ";
    $stSql .= "     mn.cod_natureza,                                                                                                                    \r\n ";
    $stSql .= "     mn.nom_natureza,                                                                                                                   \r\n ";
    $stSql .= "     mg.cod_genero,                                                                                                                       \r\n ";
    $stSql .= "     mg.nom_genero,                                                                                                                      \r\n ";
    $stSql .= "     me.cod_especie,                                                                                                                      \r\n ";
    $stSql .= "     me.nom_especie,                                                                                                                     \r\n ";
    $stSql .= "     mc.descricao_credito,                                                                                                              \r\n ";
    $stSql .= "     ag.cod_grupo,                                                                                                                          \r\n ";
    $stSql .= "     ag.ano_exercicio                                                                                                                      \r\n ";
    $stSql .= " FROM                                                                                                                                            \r\n ";
    $stSql .= "     monetario.credito as mc                                                                                                          \r\n ";
    $stSql .= " INNER JOIN monetario.especie_credito as me ON mc.cod_natureza = me.cod_natureza               \r\n ";
    $stSql .= "                                                                       AND mc.cod_genero = me.cod_genero                  \r\n ";
    $stSql .= "                                                                       AND mc.cod_especie=me.cod_especie                  \r\n ";
    $stSql .= "                                                                                                                                                     \r\n ";
    $stSql .= " INNER JOIN monetario.genero_credito as mg ON me.cod_natureza = mg.cod_natureza               \r\n ";
    $stSql .= "                                                                       AND me.cod_genero = mg.cod_genero                  \r\n ";
    $stSql .= "                                                                                                                                                     \r\n ";
    $stSql .= " INNER JOIN monetario.natureza_credito as mn ON mg.cod_natureza = mn.cod_natureza             \r\n ";
    $stSql .= "                                                                                                                                                     \r\n ";
    $stSql .= " LEFT JOIN arrecadacao.credito_grupo as ac ON ac.cod_credito = mc.cod_credito                       \r\n ";
    $stSql .= "                                                                     AND ac.cod_especie = mc.cod_especie                   \r\n ";
    $stSql .= "                                                                     AND ac.cod_genero  = mc.cod_genero                    \r\n ";
    $stSql .= "                                                                     AND ac.cod_natureza= mc.cod_natureza                 \r\n ";
    $stSql .= "                                                                                                                                                     \r\n ";
    $stSql .= " LEFT JOIN arrecadacao.grupo_credito as ag ON ag.cod_grupo = ac.cod_grupo                           \r\n ";
    $stSql .= "                                                                      AND ag.ano_exercicio = ano_exercicio                    \r\n ";

return $stSql;
}

function recuperaIndicadorCredito(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montarecuperaIndicadorCredito().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    //$this->debug();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montarecuperaIndicadorCredito()
{
    $stSql  = " SELECT                                                                                                                                      \n";
    $stSql .= "     ci.cod_indicador,                                                                                                                   \n";
    $stSql .= "     ie.descricao,                                                                                                                         \n";
    $stSql .= "     ci.timestamp                                                                                                                         \n";
    $stSql .= " FROM                                                                                                                                         \n";
    $stSql .= "     monetario.credito_indicador as ci                                                                                         \n";
    $stSql .= "     INNER JOIN monetario.indicador_economico as ie ON ie.cod_indicador = ci.cod_indicador    \n";

return $stSql;
}

} // fecha classe
