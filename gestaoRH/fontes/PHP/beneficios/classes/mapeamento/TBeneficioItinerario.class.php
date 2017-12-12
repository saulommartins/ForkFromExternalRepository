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
  * Classe de mapeamento da tabela BENEFICIO.ITINERARIO
  * Data de Criação: 07/07/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
  * Caso de uso: uc-04.06.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  BENEFICIO.ITINERARIO
  * Data de Criação: 07/07/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TBeneficioItinerario extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TBeneficioItinerario()
{
    parent::Persistente();
    $this->setTabela('beneficio.itinerario');

    $this->setCampoCod('vale_transporte_cod_vale_transporte');
    $this->setComplementoChave('');

    $this->AddCampo('vale_transporte_cod_vale_transporte','integer',true,'',true,true);
    $this->AddCampo('cod_linha_destino','integer',true,'',false,true);
    $this->AddCampo('cod_linha_origem','integer',true,'',false,true);
    $this->AddCampo('municipio_destino','integer',true,'',false,true);
    $this->AddCampo('uf_destino','integer',true,'',false,true);
    $this->AddCampo('municipio_origem','integer',true,'',false,true);
    $this->AddCampo('uf_origem','integer',true,'',false,true);

}

/**
    * Recupera os Itinerarios da tabela beneficio.itinerario
    * @access Public
*/
function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT Bi.vale_transporte_cod_vale_transporte                                    \n";
    $stSql .= "     , Sm1.nom_municipio AS municipio_origem                                     \n";
    $stSql .= "     , Sm2.nom_municipio AS municipio_destino                                    \n";
    $stSql .= "     , Bl1.descricao AS linha_origem                                             \n";
    $stSql .= "     , Bl2.descricao AS linha_destino                                            \n";
    $stSql .= "  FROM beneficio.itinerario AS Bi                                                \n";
    $stSql .= "     , beneficio.vale_transporte AS Bvt                                          \n";
    $stSql .= "     , sw_municipio AS Sm1                                                       \n";
    $stSql .= "     , sw_municipio AS Sm2                                                       \n";
    $stSql .= "     , beneficio.linha AS Bl1                                                    \n";
    $stSql .= "     , beneficio.linha AS Bl2                                                    \n";
    $stSql .= " WHERE Bi.vale_transporte_cod_vale_transporte = Bvt.cod_vale_transporte          \n";
    $stSql .= "   AND Sm1.cod_municipio = Bi.municipio_origem                                   \n";
    $stSql .= "   AND Sm1.cod_uf = Bi.uf_origem                                                 \n";
    $stSql .= "   AND Sm2.cod_municipio = Bi.municipio_destino                                  \n";
    $stSql .= "   AND Sm2.cod_uf = Bi.uf_destino                                                \n";
    $stSql .= "   AND Bl1.cod_linha = Bi.cod_linha_origem                                       \n";
    $stSql .= "   AND Bl2.cod_linha = Bi.cod_linha_destino                                      \n";

    return $stSql;
}

function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
