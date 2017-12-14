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
  * Classe de mapeamento de Carteira
  * Data de criação : 10/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: Tonismar R. Bernardo

    * $Id: TMONCarteira.class.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.05.05
**/

/*
$Log$
Revision 1.7  2006/09/15 14:46:11  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE );

class TMONCarteira extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TMONCarteira()
{
    parent::Persistente();
    $this->setTabela('monetario.carteira');

    $this->setCampoCod('cod_carteira');
    $this->setComplementoChave('cod_convenio');

    $this->AddCampo('cod_convenio','INTEGER',true ,'',true,true );
    $this->AddCampo('cod_carteira','INTEGER',true ,'',true,true);
    $this->AddCampo('num_carteira','INTEGER',false,'',true,false);
    $this->AddCampo('variacao'    ,'INTEGER',false,'',true,false);
}

function recuperaTodos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaTodos().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTodos()
{
    $stSql  = "    SELECT                   \n";
    $stSql .= "        ca.cod_convenio,     \n";
    $stSql .= "        ca.cod_carteira,     \n";
    $stSql .= "        ca.num_carteira,     \n";
    $stSql .= "        ca.variacao,         \n";
    $stSql .= "        co.num_convenio,     \n";
    $stSql .= "        tc.nom_tipo,         \n";
    $stSql .= "        tc.cod_tipo \n";
    $stSql .= "    FROM                     \n";
    $stSql .= "        monetario.carteira as ca \n";
    $stSql .= "    INNER JOIN               \n";
    $stSql .= "        monetario.convenio as co \n";
    $stSql .= "    ON                       \n";
    $stSql .= "        co.cod_convenio = ca.cod_convenio \n";
    $stSql .= "    INNER JOIN               \n";
    $stSql .= "        monetario.tipo_convenio as tc \n";
    $stSql .= "    ON                       \n";
    $stSql .= "        co.cod_tipo = tc.cod_tipo \n";

    return $stSql;
}

}
