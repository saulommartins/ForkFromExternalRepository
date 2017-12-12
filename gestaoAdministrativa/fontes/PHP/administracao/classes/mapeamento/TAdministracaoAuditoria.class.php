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
* Classe de mapeamento para administracao.auditoria
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Id: TAdministracaoAuditoria.class.php 65281 2016-05-10 12:01:27Z michel $

Casos de uso: uc-01.03.91
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TAuditoria extends Persistente
{
    public function TAuditoria()
    {
        $this->setEstrutura( array() );

        $this->setTabela('administracao.auditoria');
        $this->setComplementoChave('numcgm, cod_acao, timestamp');

        $this->AddCampo( 'numcgm'    ,'integer'  , true  ,'' , true , true  );
        $this->AddCampo( 'cod_acao'  ,'integer'  , true  ,'' , true , true  );
        $this->AddCampo( 'timestamp' ,'timestamp', false ,'' , true , false );
        $this->AddCampo( 'objeto'    ,'hstore'   , true  ,'' , false, false, '', array());
        $this->AddCampo( 'transacao' ,'boolean'  , false ,'' , false, false );
    }

    public function recuperaLinksMaisAcessados(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaLinksMaisAcessados().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaLinksMaisAcessados()
    {
        $stSql  = " SELECT                                                      \n";
        $stSql .= "     a.cod_acao                                              \n";
        $stSql .= " FROM                                                        \n";
        $stSql .= "     administracao.auditoria a                               \n";
        $stSql .= " JOIN (                                                      \n";
        $stSql .= "     SELECT                                                  \n";
        $stSql .= "         administracao.auditoria.cod_acao,                   \n";
        $stSql .= "         max(administracao.auditoria.timestamp) AS timestamp \n";
        $stSql .= "     FROM                                                    \n";
        $stSql .= "         administracao.auditoria                             \n";
        $stSql .= "     GROUP BY                                                \n";
        $stSql .= "         administracao.auditoria.cod_acao                    \n";
        $stSql .= "     ) ac                                                    \n";
        $stSql .= " ON                                                          \n";
        $stSql .= "         a.cod_acao    = ac.cod_acao                         \n";
        $stSql .= "     AND a.timestamp = ac.timestamp                          \n";
        $stSql .= " ORDER BY                                                    \n";
        $stSql .= "     a.timestamp DESC                                        \n";
        $stSql .= " LIMIT 5                                                     \n";

        return $stSql;
    }

    public function recuperaAuditoria(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $obErro = new Erro();
        $obConexao = new Conexao();
        $rsRecordSet = new RecordSet();

        if (!empty($stFiltro)) {
            $stFiltro = (!strpos($stFiltro, "WHERE")) ? " WHERE $stFiltro" : $stFiltro;
        }

        if (!empty($stOrdem)) {
            $stOrdem = (!strpos($stOrdem, "ORDER BY")) ? " ORDER BY $stOrdem" : $stOrdem;
        }

        $stSql  = " SELECT                                                                                                                                   \n";
        $stSql .= "     u.username,                                                                                                                          \n";
        $stSql .= "     u.numcgm,                                                                                                                            \n";
        $stSql .= "     a.nom_acao,                                                                                                                          \n";
        $stSql .= "     a.cod_acao,                                                                                                                          \n";
        $stSql .= "     to_char(au.timestamp, 'dd/mm/yyyy - HH24:MI:SS') as timestamp,                                                                         \n";
        $stSql .= "     au.objeto,                                                                                                                           \n";
        $stSql .= "     m.nom_modulo,                                                                                                                        \n";
        $stSql .= "     f.nom_funcionalidade                                                                                                                 \n";
        $stSql .= " FROM                                                                                                                                     \n";
        $stSql .= "     administracao.usuario as u                                                                                                           \n";
        $stSql .= " INNER JOIN                                                                                                                               \n";
        $stSql .= "     administracao.auditoria as au on au.numcgm = u.numcgm                                                                                \n";
        $stSql .= " INNER JOIN                                                                                                                               \n";
        $stSql .= "     administracao.acao as a on a.cod_acao = au.cod_acao                                                                                  \n";
        $stSql .= " INNER JOIN                                                                                                                               \n";
        $stSql .= "     administracao.funcionalidade as f on f.cod_funcionalidade = a.cod_funcionalidade                                                     \n";
        $stSql .= " INNER JOIN                                                                                                                               \n";
        $stSql .= "     administracao.modulo as m on m.cod_modulo = f.cod_modulo                                                                             \n";

        //WHERE
        $stSql .= $stFiltro;

        //ORDER BY
        $stSql .= $stOrdem;

        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function recuperaAuditoriaDetalhes(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $obErro = new Erro();
        $obConexao = new Conexao();
        $rsRecordSet = new RecordSet();

        if (!empty($stFiltro)) {
            $stFiltro = (!strpos($stFiltro, "WHERE")) ? " WHERE $stFiltro" : $stFiltro;
        }

        if (!empty($stOrdem)) {
            $stOrdem = (!strpos($stOrdem, "ORDER BY")) ? " ORDER BY $stOrdem" : $stOrdem;
        }

        $stSql  = "
                        SELECT u.username,
                               a.nom_acao,
                               to_char(au_d.timestamp, 'dd/mm/yyyy - hh:mi:ss') as timestamp,
                               REPLACE(au_d.valores::TEXT,'\\\"', '\"') AS valores,
                               m.nom_modulo,
                               f.nom_funcionalidade
                          FROM administracao.usuario as u
                    INNER JOIN administracao.auditoria_detalhe as au_d
                            ON au_d.numcgm = u.numcgm
                    INNER JOIN administracao.acao as a
                            ON a.cod_acao = au_d.cod_acao
                    INNER JOIN administracao.funcionalidade as f
                            ON f.cod_funcionalidade = a.cod_funcionalidade
                    INNER JOIN administracao.modulo as m
                            ON m.cod_modulo = f.cod_modulo
        ";

        //WHERE
        $stSql .= $stFiltro;

        //ORDER BY
        $stSql .= $stOrdem;

        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

}
