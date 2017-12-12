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
* Classe de Mapeamento para tabela organograma_local
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 21799 $
$Name$
$Author: cassiano $
$Date: 2007-04-12 16:18:38 -0300 (Qui, 12 Abr 2007) $

Casos de uso: uc-01.05.01, uc-01.05.02, uc-01.05.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ORGANOGRAMA.LOCAL
  * Data de Criação: 07/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TOrganogramaLocal extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrganogramaLocal()
{
    parent::Persistente();
    $this->setTabela('organograma.local');

    $this->setCampoCod('cod_local');
    $this->setComplementoChave('');

    $this->AddCampo('cod_local','integer',true,'',true,false);
    $this->AddCampo('cod_logradouro','integer',true,'',false,true);
    $this->AddCampo('numero','integer',false,'',false,false);
    $this->AddCampo('fone','char',true,'12',false,false);
    $this->AddCampo('ramal','integer',false,'',false,false);
    $this->AddCampo('dificil_acesso','boolean',false,'',false,false);
    $this->AddCampo('insalubre','boolean',false,'',false,false);
    $this->AddCampo('descricao','varchar',true,'80',false,false);

}

function recuperaRelacionamentoLogradouro(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaRelacionamentoLogradouro().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaRelacionamentoRelatorio(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaRelatorio().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoLogradouro()
{
    $stSQL .= " SELECT                                                \n";
    $stSQL .= "     TL.cod_tipo,                                      \n";
    $stSQL .= "     TL.nom_tipo||' '||NL.nom_logradouro as tipo_nome, \n";
    $stSQL .= "     NL.nom_logradouro,                                \n";
    $stSQL .= "     L.*,                                              \n";
    $stSQL .= "     M.nom_municipio,                                  \n";
    $stSQL .= "     U.nom_uf,                                         \n";
    $stSQL .= "     T.sequencia,                                      \n";
    $stSQL .= "     CASE WHEN T.prox_sequencia IS NULL THEN 1 ELSE T.prox_sequencia END AS prox_sequencia, \n";
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
    $stSQL .= "     (                                                 \n";
    $stSQL .= "       SELECT                                          \n";
    $stSQL .= "           T.*,                                        \n";
    $stSQL .= "           ( MT.sequencia + 1 ) AS prox_sequencia      \n";
    $stSQL .= "       FROM                                            \n";
    $stSQL .= "           imobiliario.trecho AS T,                        \n";
    $stSQL .= "           (                                           \n";
    $stSQL .= "            SELECT                                     \n";
    $stSQL .= "                MAX(sequencia) AS sequencia,           \n";
    $stSQL .= "                cod_logradouro                         \n";
    $stSQL .= "            FROM                                       \n";
    $stSQL .= "                imobiliario.trecho                         \n";
    $stSQL .= "            GROUP BY                                   \n";
    $stSQL .= "                cod_logradouro                         \n";
    $stSQL .= "           ) AS MT                                     \n";
    $stSQL .= "       WHERE                                           \n";
    $stSQL .= "           T.cod_logradouro = MT.cod_logradouro AND    \n";
    $stSQL .= "           T.sequencia      = MT.sequencia             \n";
    $stSQL .= "     ) AS T                                            \n";
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

function montaRecuperaRelatorio()
{
    $stSQL .= "SELECT \n";
    $stSQL .= " L.descricao, \n";
    $stSQL .= " case when L.insalubre is true then 'Sim' \n";
    $stSQL .= " else 'Não' end as insalubre, \n";
    $stSQL .= " L.fone,L.ramal, \n";
    $stSQL .= " case when L.dificil_acesso is true then 'Sim' \n";
    $stSQL .= " else 'Não' end as dificil_acesso, \n";
    $stSQL .= " NL.nom_logradouro as endereco, \n";
    $stSQL .= " L.numero as numero \n";
    $stSQL .= " from organograma.local L \n";
    $stSQL .= " left join sw_nome_logradouro NL \n";
    $stSQL .= " on NL.cod_logradouro=L.cod_logradouro \n";
    $stSQL .= " ORDER BY descricao \n";

    return $stSQL;
}

function recuperaLocalizacao(&$rsRecordSet,$stFiltro="",$stOrder="ORDER BY local.descricao",$boTransacao="")
{
         return $this->executaRecupera("montaRecuperaLocalizacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaLocalizacao()
    {
        $stSql = "
            SELECT local.cod_local
                 , local.descricao as nom_local
              FROM organograma.local
             WHERE 1=1 ";

        return $stSql;
    }

function recuperaTodosTotalizado(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stGroup =" GROUP BY local.cod_local
                        ,cod_logradouro
                        ,numero
                        ,fone
                        ,ramal
                        ,dificil_acesso
                        ,insalubre
                        ,local.descricao
                ";
    $stSql  = $this->montaRecuperaTodosTotalizado().$stFiltro.$stGroup.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTodosTotalizado()
{
    $stSQL .= " SELECT  DISTINCT
                    local.cod_local
                    ,cod_logradouro
                    ,numero
                    ,fone
                    ,ramal
                    ,dificil_acesso
                    ,insalubre
                    ,local.descricao
                    ,COUNT(historico_bem.cod_bem) as total
                    
                FROM organograma.local

                INNER JOIN (SELECT  MAX(TIMESTAMP) as TIMESTAMP
                                    ,cod_bem
                                    ,cod_local
                                    ,cod_orgao                                    
                                FROM patrimonio.historico_bem
                                GROUP BY cod_bem, cod_local, cod_orgao
                ) as historico_bem
                    ON historico_bem.cod_local = local.cod_local
                                
                INNER JOIN patrimonio.bem
                    ON bem.cod_bem = historico_bem.cod_bem

                LEFT JOIN patrimonio.bem_baixado
                    ON bem_baixado.cod_bem = bem.cod_bem
            ";

            if ($this->getDado('acao') == 'alterar') {
                $stSQL .= " LEFT JOIN patrimonio.inventario_historico_bem
                                ON inventario_historico_bem.cod_bem     = historico_bem.cod_bem
                            ";
            }
    
    return $stSQL;
}

}
