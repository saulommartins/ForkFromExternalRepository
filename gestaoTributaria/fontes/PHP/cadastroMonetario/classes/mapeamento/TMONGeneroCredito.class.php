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
    * Classe de regra de negocio para MONETARIO.GENERO
    * Data de Criacao: 20/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @package URBEM
    * @subpackage Regra

    * $Id: TMONGeneroCredito.class.php 59612 2014-09-02 12:00:51Z gelson $

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

class TMONGeneroCredito extends Persistente
{
/**
    * Metodo Construtor
    * @access Private
*/
function TMONGeneroCredito()
{
    parent::Persistente();
    $this->setTabela('monetario.genero_credito');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_natureza,cod_genero');

    $this->AddCampo('cod_natureza','INTEGER',true,'',true,true);
    $this->AddCampo('cod_genero','INTEGER',true,'',true,false);
    $this->AddCampo('nom_genero','VARCHAR',true,'80',false,false);

}

function recuperaGeneroNatureza(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaGeneroNatureza().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}
function montaRecuperaGeneroNatureza()
{
    $stSql  = "    SELECT                                           \r\n";
    $stSql .= "        DISTINCT                                     \r\n";
    $stSql .= "        gc.nom_genero   ,                            \r\n";
    $stSql .= "        gc.cod_genero    ,                           \r\n";
    $stSql .= "        gc.cod_natureza                              \r\n";
    $stSql .= "    FROM                                             \r\n";
    $stSql .= "        monetario.genero_credito as gc               \r\n";

    $stSql .= "    INNER JOIN                                       \r\n";
    $stSql .= "        monetario.especie_credito as ec              \r\n";
    $stSql .= "    ON                                               \r\n";
    $stSql .= "        gc.cod_genero = ec.cod_genero                \r\n";

    $stSql .= "    INNER JOIN                                       \r\n";
    $stSql .= "        monetario.natureza_credito as nc             \r\n";
    $stSql .= "    ON                                               \r\n";
    $stSql .= "        gc.cod_natureza = nc.cod_natureza            \r\n";
    $stSql .= "    AND                                              \r\n";
    $stSql .= "        ec.cod_natureza = nc.cod_natureza            \r\n";

    return $stSql;
}

}
