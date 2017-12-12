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
     * Classe de mapeamento para a tabela IMOBILIARIO.AREA_CONSTRUCAO
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerir

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMAreaConstrucao.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.11
                     uc-05.01.12
*/

/*
$Log$
Revision 1.5  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.AREA_CONSTRUCAO
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMAreaConstrucao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMAreaConstrucao()
{
    parent::Persistente();
    $this->setTabela('imobiliario.area_construcao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_construcao,timestamp');

    $this->AddCampo('cod_construcao','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
    $this->AddCampo('area_real','numeric',true,'14,2',false,false);

}
function recuperaTimestampConstrucao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaTimestampConstrucao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTimestampConstrucao()
{
    $stSQL .= " SELECT                                                      \n";
    $stSQL .= "     max(timestamp) as timestamp_construcao                  \n";
    $stSQL .= " FROM                                                        \n";
    $stSQL .= "     imobiliario.area_construcao AS cp                       \n";

    return $stSQL;

}

function recuperaAreaConstrucao(&$rsRecordSet, $stCondicao = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAreaConstrucao( $stCondicao );
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAreaConstrucao($stCondicao)
{
    $stSQL .= "     SELECT                                                                                   \n";
    $stSQL .= "         imobiliario.fn_calcula_area_imovel( inscricao_municipal ) AS area_imovel,            \n";
    $stSQL .= "         imobiliario.fn_calcula_area_imovel_lote( inscricao_municipal ) AS area_imovel_lote,  \n";
    $stSQL .= "         imobiliario.fn_calcula_area_imovel_construcao( inscricao_municipal ) AS area_total   \n";
    $stSQL .= "     FROM                                                                                     \n";
    $stSQL .= "         imobiliario.imovel                                                                   \n";
    $stSQL .= "     WHERE                                                                                    \n";
    $stSQL .=           $stCondicao."                                                                        \n";

    return $stSQL;
}

}//fecha classe
