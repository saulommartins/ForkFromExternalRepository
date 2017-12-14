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
    * Classe de mapeamento da tabela contabilidade.nota_explicativa_acao
    * Data de Criação: 03/09/2007

    * @author Analista      : Gelson Gonçalves
    * @author Desenvolvedor : Rodrigo S. Rodrigues

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-02.02.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TContabilidadeNotasExplicativas extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TContabilidadeNotasExplicativas()
{
    parent::Persistente();
    $this->setTabela("contabilidade.nota_explicativa");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_acao');

    $this->AddCampo('cod_acao','integer',true,'',true,true);
    $this->AddCampo('nota_explicativa','text',true,'',false,false);
    $this->AddCampo('dt_inicial','date',true,'',false,false);
    $this->AddCampo('dt_final','date',true,'',false,false);
}

/**
    * Seta dados para o recuperaAnexo
    * @access Public
    * @return String $stSql
*/
function montaRecuperaAnexo()
{
    $stSQL .= "
         SELECT A.nom_acao
              , A.complemento_acao
              , A.cod_acao
           FROM administracao.acao AS A
              , contabilidade.nota_explicativa_acao AS EA
              , administracao.permissao As p
          WHERE A.cod_acao = EA.cod_acao
            AND p.cod_acao = EA.cod_acao
            AND p.ano_exercicio = '".Sessao::getExercicio()."'
    ";
    if(Sessao::getExercicio() >= 2013){
        $stSQL .= "AND p.cod_acao NOT IN (681) \n";
    }
    if ( $this->getDado('cod_acao') ) {
        $stSQL .= " AND A.cod_acao = ".$this->getDado('cod_acao')." \n";
    }
    $stSQL .= "
          GROUP BY A.nom_acao
              , A.complemento_acao
              , A.cod_acao
          ORDER BY A.nom_acao
    ";
    return $stSQL;
}

function recuperaAnexo(&$rsRecordSet,$stFiltro='',$stOrdem='', $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaAnexo().$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaNotaExplicativa()
{
    $stSQL   = " SELECT                                             \n";
    $stSQL  .= "         NE.cod_acao,                               \n";
    $stSQL  .= "         NE.nota_explicativa,                       \n";
    $stSQL  .= "         NE.dt_inicial,                             \n";
    $stSQL  .= "         NE.dt_final,                               \n";
    $stSQL  .= "          A.nom_acao,                               \n";
    $stSQL  .= "          A.complemento_acao                        \n";
    $stSQL  .= " FROM                                               \n";
    $stSQL  .= "         administracao.acao AS A,                   \n";
    $stSQL  .= "         contabilidade.nota_explicativa_acao AS EA, \n";
    $stSQL  .= "         contabilidade.nota_explicativa AS NE       \n";
    $stSQL  .= " WHERE                                              \n";
    $stSQL  .= "         EA.cod_acao = A.cod_acao                   \n";
    $stSQL  .= " AND     NE.cod_acao = EA.cod_acao                  \n";
    $stSQL  .= " ORDER BY A.nom_acao                                \n";

    return $stSQL;
}

function recuperaNotaExplicativa(&$rsRecordSet,$stFiltro='',$stOrdem='', $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaNotaExplicativa().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaNotaExplicativaRelatorio()
{
    $stSQL  = "  SELECT nota_explicativa.cod_acao           \n";
    $stSQL .= "       , nota_explicativa.nota_explicativa   \n";
    $stSQL .= "       , to_char(to_date(nota_explicativa.dt_inicial::varchar, 'yyyy-mm-dd'), 'dd/mm/yyyy') \n";
    $stSQL .= "       || ' até ' || to_char(to_date(nota_explicativa.dt_final::varchar, 'yyyy-mm-dd'), 'dd/mm/yyyy') as periodo\n";
    $stSQL .= "       , acao.nom_acao                       \n";
    $stSQL .= "    FROM contabilidade.nota_explicativa      \n";
    $stSQL .= "    JOIN contabilidade.nota_explicativa_acao \n";
    $stSQL .= "      ON nota_explicativa.cod_acao = nota_explicativa_acao.cod_acao \n";
    $stSQL .= "    JOIN administracao.acao                  \n";
    $stSQL .= "      ON acao.cod_acao = nota_explicativa_acao.cod_acao \n";
    $stSQL .= "   WHERE true                                \n";
    if ( $this->getDado('cod_acao') ) {
        $stSQL .= " AND nota_explicativa.cod_acao = ".$this->getDado('cod_acao')." \n";
    }
    if ( $this->getDado('dt_inicial') ) {
        $stSQL .= " AND nota_explicativa.dt_inicial = '".$this->getDado('dt_inicial')."' \n";
    }
    if ( $this->getDado('dt_final') ) {
        $stSQL .= " AND nota_explicativa.dt_final = '".$this->getDado('dt_final')."' \n";
    }

    return $stSQL;
}

function recuperaNotaExplicativaRelatorio(&$rsRecordSet,$stFiltro='',$stOrdem='', $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaNotaExplicativaRelatorio().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}

?>
