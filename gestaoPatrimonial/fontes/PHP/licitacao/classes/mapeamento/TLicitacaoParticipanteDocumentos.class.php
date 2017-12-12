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
    * Classe de mapeamento da tabela licitacao.participante_documentos
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 21723 $
    $Name$
    $Author: andre.almeida $
    $Date: 2007-04-10 15:36:22 -0300 (Ter, 10 Abr 2007) $

    * Casos de uso: uc-03.05.18
                uc-03.05.19
*/
/*
$Log$
Revision 1.6  2007/04/10 18:31:53  andre.almeida
Adicionado consultas para o e-Sfinge.

Revision 1.5  2007/01/18 16:21:18  tonismar
bug #8079

Revision 1.4  2007/01/16 17:43:48  hboaventura
Bug #8076#

Revision 1.3  2006/11/20 17:13:55  fernando
Alterações para serem usados no caso de uso de habilitação de participante.

Revision 1.2  2006/11/08 10:51:42  larocca
Inclusão dos Casos de Uso

Revision 1.1  2006/09/15 12:05:59  cleisson
inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.participante_documentos
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoParticipanteDocumentos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TLicitacaoParticipanteDocumentos()
{
    parent::Persistente();
    $this->setTabela("licitacao.participante_documentos");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_licitacao,cod_documento,dt_validade,cgm_fornecedor,cod_modalidade,cod_entidade,exercicio_entidade');

    $this->AddCampo('cod_licitacao'     ,'integer',false ,''   ,true,'TLicitacaoParticipante');
    $this->AddCampo('cod_documento'     ,'integer',false ,''   ,true,'TLicitacaoLicitacaoDocumentos');
    $this->AddCampo('num_documento'     ,'char'   ,false ,'30' ,true,'TLicitacaoLicitacaoDocumentos');
    $this->AddCampo('dt_validade'       ,'date'   ,false ,''   ,true,false);
    $this->AddCampo('dt_emissao'        ,'date'   ,false ,''   ,true,false);
    $this->AddCampo('cgm_fornecedor'    ,'integer',false ,''   ,true,'TLicitacaoParticipante');
    $this->AddCampo('cod_modalidade'    ,'integer',false ,''   ,true,'TLicitacaoParticipante');
    $this->AddCampo('cod_entidade'      ,'integer',false ,''   ,true,'TLicitacaoParticipante');
    $this->AddCampo('exercicio'         ,'char'   ,false ,'4'  ,true,'TLicitacaoLicitacao','exercicio');
}

function recuperaParticipanteLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaParticipanteLicitacao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaParticipanteLicitacao()
{
    $stSql  ="SELECT                                                          \n";
    $stSql .="     ll.cod_entidade                                            \n";
    $stSql .="    ,ll.cod_modalidade                                          \n";
    $stSql .="    ,ll.exercicio                                               \n";
    $stSql .="    ,le.cod_licitacao                                           \n";
    $stSql .="    ,ll.cod_objeto                                              \n";
    $stSql .="    ,cgm.numcgm                                                 \n";
    $stSql .="    ,cgm.nom_cgm                                                \n";
    $stSql .="FROM                                                            \n";
    $stSql .="     licitacao.licitacao as ll                                  \n";
    $stSql .="     left join licitacao.edital as le on (ll.cod_licitacao = le.cod_licitacao AND ll.cod_modalidade = le.cod_modalidade AND ll.cod_entidade = le.cod_entidade AND ll.exercicio = le.exercicio )\n";
    $stSql .="    ,licitacao.participante as lp                               \n";
    $stSql .="    ,sw_cgm as cgm                                              \n";
    $stSql .="WHERE                                                           \n";
    $stSql .="    ll.cod_licitacao      = lp.cod_licitacao                    \n";
    $stSql .="    AND ll.cod_modalidade = lp.cod_modalidade                   \n";
    $stSql .="    AND ll.cod_entidade   = lp.cod_entidade                     \n";
    $stSql .="    AND ll.exercicio      = lp.exercicio                        \n";
    $stSql .="    AND lp.cgm_fornecedor = cgm.numcgm                          \n";

    if ($this->getDado('num_edital')) {
      $stSql .="    AND le.num_edital = ".$this->getDado('num_edital')."      \n";
    }

    if ( $this->getDado('cod_licitacao') ) {
        $stSql .= ' AND le.cod_licitacao = '. $this->getDado('cod_licitacao')."\n";
    }

    if ( $this->getDado('cgm_fornecedor') ) {
        $stSql .= ' AND lp.cgm_fornecedor = '. $this->getDado('cgm_fornecedor')."\n";
    }

    if ( $this->getDado('cod_modalidade') ) {
        $stSql .= ' AND ll.cod_modalidade = '. $this->getDado('cod_modalidade')."\n";
    }

    if ( $this->getDado('cod_entidade') ) {
        $stSql .= ' AND ll.cod_entidade = '. $this->getDado('cod_entidade')."\n";
    }

    if ( $this->getDado('exercicio') ) {
        $stSql .= ' AND ll.exercicio = '. $this->getDado('exercicio')."\n";
    }

    $stSql .="    order by cgm.numcgm                                         \n";

    return $stSql;
}

function recuperaDocumentoUtilizado(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDocumentoUtilizado().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaDocumentoUtilizado()
{
    $stSql =" select * ";
    $stSql.="   from licitacao.participante_documentos";
    $stSql.="  where cod_licitacao = ".$this->getDado('cod_licitacao')."        \n";
    $stSql.="    and cod_documento =".$this->getDado('num_documento')."         \n";
    $stSql.="    and exercicio     = '".$this->getDado('exercicio')."'          \n";
    $stSql.="    and cod_entidade  = ".$this->getDado('cod_entidade')."         \n";
    $stSql.="    and cod_modalidade = ".$this->getDado('cod_modalidade')."      \n";

    return $stSql;
}

/**
 * Retorna os participantes
 */
 function recuperaParticipantes(&$rsRecordSet)
 {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamento().
             " WHERE participante.cod_licitacao=".$this->getDado('cod_licitacao');
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", "" );
}

function recuperaDocumentoParticipante(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDocumentoParticipante().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaDocumentoParticipante()
{
    $stSql  ="SELECT                                        \n";
    $stSql .="     pd.cod_documento                         \n";
    $stSql .="    ,ld.nom_documento                         \n";
    $stSql .="    ,pd.num_documento                         \n";
    $stSql .="    ,to_char(pd.dt_validade,'dd/mm/yyyy')  as dt_validade   \n";
    $stSql .="    ,cgm.numcgm                               \n";
    $stSql .="FROM                                          \n";
    $stSql .="    licitacao.documento as ld                 \n";
    $stSql .="   ,licitacao.participante_documentos as pd   \n";
    $stSql .="   ,sw_cgm as cgm                             \n";
    $stSql .="   ,licitacao.participante as lp              \n";
    $stSql .="   ,licitacao.edital as le                    \n";
    $stSql .="WHERE                                         \n";
    $stSql .="       pd.cod_licitacao = lp.cod_licitacao    \n";
    $stSql .="   and pd.cgm_fornecedor = cgm.numcgm         \n";
    $stSql .="   and pd.cod_documento = ld.cod_documento    \n";
    $stSql .="   and pd.cod_modalidade = lp.cod_modalidade  \n";
    $stSql .="   and pd.cod_entidade = lp.cod_entidade      \n";
    $stSql .="   and pd.exercicio = lp.exercicio            \n";

    if ($this->getDado('num_edital')) {
      $stSql .="    AND le.num_edital = ".$this->getDado('num_edital')."\n";
    }

    if ($this->getDado('numcgm')) {
      $stSql .="    AND cgm.numcgm    = ".$this->getDado('numcgm')."\n";
    }

    return $stSql;

  }//fim da function

    public function recuperaCertidaoParticipanteEsfinge(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera( "montaRecuperaCertidaoParticipanteEsfinge", $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    }

    public function montaRecuperaCertidaoParticipanteEsfinge()
    {
        $stSql = "
            select licitacao.cod_licitacao
                 , case when sw_cgm_pessoa_fisica.numcgm is not null then '01'
                        when sw_cgm_pessoa_juridica.numcgm is not null then '02'
                        else '00'
                   end as tipo_pessoa
                 , case when sw_cgm_pessoa_fisica.numcgm is not null then sw_cgm_pessoa_fisica.cpf
                        when sw_cgm_pessoa_juridica.numcgm is not null then sw_cgm_pessoa_juridica.cnpj
                        else ''
                   end as cic_participante
                 , tipo_certidao_esfinge.cod_tipo_certidao
                 , participante_documentos.num_documento
                 , to_char(participante_documentos.dt_emissao,'dd/mm/yyyy') as dt_emissao
                 , to_char(participante_documentos.dt_validade,'dd/mm/yyyy') as dt_validade
              from licitacao.licitacao

            join licitacao.participante
            on participante.cod_licitacao = licitacao.cod_licitacao
            and participante.cod_modalidade = licitacao.cod_modalidade
            and participante.cod_entidade = licitacao.cod_entidade
            and participante.exercicio = licitacao.exercicio

            join sw_cgm
            on sw_cgm.numcgm = participante.cgm_fornecedor

            left join sw_cgm_pessoa_fisica
            on sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

            left join sw_cgm_pessoa_juridica
            on sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm

            join licitacao.participante_documentos
            on participante_documentos.cod_licitacao = participante.cod_licitacao
            and participante_documentos.cgm_fornecedor = participante.cgm_fornecedor
            and participante_documentos.cod_modalidade = participante.cod_modalidade
            and participante_documentos.cod_entidade = participante.cod_entidade
            and participante_documentos.exercicio = participante.exercicio

            join tcesc.tipo_certidao_esfinge
            on tipo_certidao_esfinge.cod_documento = participante_documentos.cod_documento

            where licitacao.exercicio = '".$this->getDado( 'exercicio' )."'
            and licitacao.cod_entidade in ( ".$this->getDado( 'cod_entidade' )." )
            and licitacao.timestamp >= to_date( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' )
            and licitacao.timestamp <= to_date( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )
        ";

        return $stSql;
    }

}//fim da classe
