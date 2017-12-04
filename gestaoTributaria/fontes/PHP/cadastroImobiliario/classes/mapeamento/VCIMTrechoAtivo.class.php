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
     * Classe de mapeamento para a tabela IMOBILIARIO.TRECHO
     * Data de Criação: 27/10/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerir

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: VCIMTrechoAtivo.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.06
*/

/*
$Log$
Revision 1.5  2006/09/18 09:12:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.TRECHO
  * Data de Criação: 27/10/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class VCIMTrechoAtivo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function VCIMTrechoAtivo()
{
    parent::Persistente();
    $this->setTabela('imobiliario.vw_trecho_ativo');

    $this->setCampoCod('cod_trecho');
    $this->setComplementoChave('cod_logradouro');

    $this->AddCampo('cod_trecho','integer',true,'',true,false);
    $this->AddCampo('cod_logradouro','integer',true,'',true,true);
    $this->AddCampo('sequencia','integer',true,'',false,false);
    $this->AddCampo('extensao','numeric',true,'8,2',false,false);
}

function montaRecuperaRelacionamento()
{
    $stSQL  = " SELECT                                                \n";
    $stSQL .= "     TL.cod_tipo,                                      \n";
    $stSQL .= "     TL.nom_tipo||' '||NL.nom_logradouro as tipo_nome, \n";
    $stSQL .= "     NL.cod_logradouro||'.'||T.sequencia AS codigo_sequencia,     \n";
    $stSQL .= "     NL.nom_logradouro,                                \n";
    $stSQL .= "     NL.cod_logradouro,                                \n";
    $stSQL .= "     L.*,                                              \n";
    $stSQL .= "     M.nom_municipio,                                  \n";
    $stSQL .= "     U.nom_uf,                                         \n";
    $stSQL .= "     T.sequencia,                                      \n";
    $stSQL .= "     T.extensao                                        \n";
    $stSQL .= " FROM                                                  \n";
    $stSQL .= "    sw_tipo_logradouro AS TL,                         \n";
    $stSQL .= "    sw_nome_logradouro AS NL,                         \n";
    $stSQL .= "    sw_municipio       AS M,                          \n";
    $stSQL .= "    sw_uf              AS U,                          \n";
    $stSQL .= "    sw_logradouro      AS L,                          \n";
    $stSQL .= "     ( SELECT                                          \n";
    $stSQL .= "           MAX(timestamp) AS timestamp,                \n";
    $stSQL .= "           cod_logradouro                              \n";
    $stSQL .= "       FROM                                            \n";
    $stSQL .= "           sw_nome_logradouro                         \n";
    $stSQL .= "       GROUP BY cod_logradouro                         \n";
    $stSQL .= "       ORDER BY cod_logradouro                         \n";
    $stSQL .= "     ) AS MNL                                          \n";
    $stSQL .= " LEFT JOIN                                             \n";
    $stSQL .= "     imobiliario.vw_trecho_ativo AS T                      \n";
    $stSQL .= " ON                                                    \n";
    $stSQL .= "     T.cod_logradouro = MNL.cod_logradouro             \n";
    $stSQL .= " WHERE                                                 \n";
    $stSQL .= "     L.cod_logradouro  = NL.cod_logradouro  AND        \n";
    $stSQL .= "     NL.cod_logradouro = MNL.cod_logradouro AND        \n";
    $stSQL .= "     NL.timestamp      = MNL.timestamp      AND        \n";
    $stSQL .= "     L.cod_municipio   = M.cod_municipio    AND        \n";
    $stSQL .= "     L.cod_uf          = M.cod_uf           AND        \n";
    $stSQL .= "     M.cod_uf          = U.cod_uf           AND        \n";
    $stSQL .= "     NL.cod_tipo       = TL.cod_tipo                   \n";

    return $stSQL;
}

function recuperaTrechos(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY vw_trecho_ativo.cod_logradouro, vw_trecho_ativo.sequencia ";
    $stSql  = $this->montaRecuperaTrechos().$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTrechos()
{
    $stSQL = "
                SELECT vw_trecho_ativo.cod_trecho
                     , vw_trecho_ativo.cod_logradouro
                     , sw_tipo_logradouro.nom_tipo
                     , sw_nome_logradouro.nom_logradouro
                     , sw_tipo_logradouro.nom_tipo  ||' '|| sw_nome_logradouro.nom_logradouro   AS tipo_nome
                     , sw_logradouro.cod_logradouro ||'.'|| vw_trecho_ativo.sequencia           AS codigo_sequencia
                     , vw_trecho_ativo.sequencia                                                AS sequencia
                     , vw_trecho_ativo.extensao
                FROM imobiliario.vw_trecho_ativo
                JOIN sw_logradouro
                  ON sw_logradouro.cod_logradouro = vw_trecho_ativo.cod_logradouro
                JOIN (
                        SELECT sw_nome_logradouro.cod_logradouro
                            , sw_nome_logradouro.nom_logradouro
                            , sw_nome_logradouro.cod_tipo
                        FROM sw_nome_logradouro
                        JOIN (
                                SELECT MAX(timestamp) AS timestamp
                                     , cod_logradouro
                                FROM sw_nome_logradouro
                                GROUP BY cod_logradouro
                            ) AS max_nome
                          ON max_nome.cod_logradouro = sw_nome_logradouro.cod_logradouro
                         AND max_nome.timestamp      = sw_nome_logradouro.timestamp
                    ) AS sw_nome_logradouro
                  ON sw_nome_logradouro.cod_logradouro = sw_logradouro.cod_logradouro
                JOIN sw_tipo_logradouro
                  ON sw_tipo_logradouro.cod_tipo = sw_nome_logradouro.cod_tipo
           LEFT JOIN imobiliario.confrontacao_trecho
                  ON confrontacao_trecho.cod_trecho     = vw_trecho_ativo.cod_trecho
                 AND confrontacao_trecho.cod_logradouro = vw_trecho_ativo.cod_logradouro
           LEFT JOIN imobiliario.imovel_confrontacao
                  ON imovel_confrontacao.cod_confrontacao = confrontacao_trecho.cod_confrontacao
                 AND imovel_confrontacao.cod_lote         = confrontacao_trecho.cod_lote
               WHERE 1=1
            ";

    return $stSQL;
}
}
