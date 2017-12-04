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
    * Classe de mapeamento da tabela licitacao.licitacao_documentos
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 28537 $
    $Name$
    $Author: luiz $
    $Date: 2008-03-13 14:07:19 -0300 (Qui, 13 Mar 2008) $

    * Casos de uso: uc-03.05.12
*/
/*
$Log$
Revision 1.8  2007/01/17 18:49:10  gelson
Adicionado o caso de uso correto.

Revision 1.7  2006/11/16 18:21:01  fernando
alterações para o alterar processo licitatorio

Revision 1.6  2006/11/14 18:18:38  fernando
função para trazer os documentos da licitacao

Revision 1.5  2006/11/09 17:20:04  fernando
alteração conforme as alterações feitas na tabela.

Revision 1.3  2006/11/03 18:28:13  fernando
ajustando conforme a tabela no banco

Revision 1.2  2006/09/22 14:41:47  leandro.zis
ajustes devido a alteração da coluna 'exercicio_entidade' da tabela licitacao.licitacao para exercicio

Revision 1.1  2006/09/15 12:05:59  cleisson
inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.licitacao_documentos
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoLicitacaoDocumentos extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */
    public function TLicitacaoLicitacaoDocumentos()
    {
            parent::Persistente();
            $this->setTabela("licitacao.licitacao_documentos");

            $this->setCampoCod('');
            $this->setComplementoChave('cod_documento,cod_licitacao,cod_modalidade,cod_entidade,exercicio');

            $this->AddCampo('cod_documento'     ,'integer',false ,''   ,true,'TLicitacaoDocumento');
            $this->AddCampo('cod_licitacao'     ,'integer',false ,''   ,true,'TLicitacaoLicitacao');
            $this->AddCampo('cod_modalidade'    ,'integer',false ,''   ,true,'TLicitacaoLicitacao');
            $this->AddCampo('cod_entidade'      ,'integer',false ,''   ,true,'TLicitacaoLicitacao');
            $this->AddCampo('exercicio'         , 'char'   ,false ,'4'  ,true,'TLicitacaoLicitacao');
    }

   public function recuperaDocumentosLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
   {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDocumentosLicitacao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaDocumentosLicitacao()
    {
        $stSql  ="SELECT ld.cod_documento                                                       \n";
        $stSql .="          , d.nom_documento                                                   \n";
        $stSql .="          , ld.cod_licitacao                                                  \n";
        $stSql .="          , ld.cod_modalidade                                                 \n";
        $stSql .="          , ld.cod_entidade                                                   \n";
        $stSql .="          , ld.exercicio                                                      \n";
        $stSql .="FROM licitacao.licitacao_documentos as ld                                     \n";
        $stSql .="        , licitacao.documento as d                                            \n";
        $stSql .="WHERE ld.cod_documento = d.cod_documento                                      \n";
        if ($this->getDado('cod_modalidade'))
            $stSql .="         AND ld.cod_modalidade = ".$this->getDado('cod_modalidade')."     \n";
        if ($this->getDado('exercicio'))
            $stSql .="         AND ld.exercicio = '".$this->getDado('exercicio')."'             \n";
        if ($this->getDado('cod_entidade'))
            $stSql .="         AND ld.cod_entidade = ".$this->getDado('cod_entidade')."         \n";
        if ($this->getDado('cod_licitacao'))
            $stSql .="         AND ld.cod_licitacao = ".$this->getDado('cod_licitacao')."       \n";

        return $stSql;
    }

    public function recuperaDocumentosLicitacaoFornecedor(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDocumentosLicitacaoFornecedor().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaDocumentosLicitacaoFornecedor()
    {
            $stSql  ="SELECT ld.cod_documento                                                           \n";
            $stSql .="    	   , d.nom_documento                                                        \n";
            $stSql .="           , ld.cod_licitacao                                                     \n";
            $stSql .="    	   , ld.cod_modalidade                                                      \n";
            $stSql .="           , ld.cod_entidade                                                      \n";
            $stSql .="           , ld.exercicio                                                         \n";
            $stSql .="           , TO_CHAR(pd.dt_validade,'dd/mm/yyyy') as dt_validade                  \n";
            $stSql .="           , TO_CHAR(pd.dt_emissao,'dd/mm/yyyy') as dt_emissao                    \n";
            $stSql .="           , pd.num_documento                                                     \n";
            $stSql .="FROM licitacao.licitacao_documentos as ld                                         \n";
            $stSql .="        , licitacao.documento as d                                                \n";
            $stSql .="        , licitacao.participante_documentos as pd                                 \n";
            $stSql .="WHERE ld.cod_documento = d.cod_documento                                          \n";
            if ($this->getDado('cod_modalidade'))
                $stSql .="      AND ld.cod_modalidade = ".$this->getDado('cod_modalidade')."            \n";
            if ($this->getDado('exercicio'))
                $stSql .="      AND ld.exercicio = '".$this->getDado('exercicio')."'                    \n";
            if ($this->getDado('cod_entidade'))
                $stSql .="      AND ld.cod_entidade = ".$this->getDado('cod_entidade')."                \n";
            if ($this->getDado('cod_licitacao'))
                $stSql .="      AND ld.cod_licitacao = ".$this->getDado('cod_licitacao')."              \n";
            $stSql .="          AND  ld.cod_documento = pd.cod_documento                                \n";
            $stSql .="          AND  ld.cod_modalidade = pd.cod_modalidade                              \n";
            $stSql .="          AND  ld.cod_entidade = pd.cod_entidade                                  \n";
            $stSql .="          AND  ld.cod_licitacao = pd.cod_licitacao                                \n";

            return $stSql;
    }

    public function recuperaDocumentosCompraDiretaFornecedor(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;
            $stSql = $this->montaRecuperaDocumentosCompraDiretaFornecedor().$stFiltro.$stOrdem;
            $this->stDebug = $stSql;
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaDocumentosCompraDiretaFornecedor()
    {
        $stSql  ="SELECT ld.cod_documento                                                           \n";
        $stSql .="    	   , d.nom_documento                                                        \n";
        $stSql .="           , ld.cod_licitacao                                                     \n";
        $stSql .="    	   , ld.cod_modalidade                                                      \n";
        $stSql .="           , ld.cod_entidade                                                      \n";
        $stSql .="           , ld.exercicio                                                         \n";
        $stSql .="           , TO_CHAR(pd.dt_validade,'dd/mm/yyyy') as dt_validade                  \n";
        $stSql .="           , TO_CHAR(pd.dt_emissao,'dd/mm/yyyy') as dt_emissao                    \n";
        $stSql .="           , pd.num_documento                                                     \n";
        $stSql .="FROM licitacao.licitacao_documentos as ld                                         \n";
        $stSql .="        , licitacao.documento as d                                                \n";
        $stSql .="        , licitacao.participante_documentos as pd                                 \n";
        $stSql .="WHERE ld.cod_documento = d.cod_documento                                          \n";

        if ($this->getDado('cod_modalidade'))
            $stSql .="      AND ld.cod_modalidade = ".$this->getDado('cod_modalidade')."            \n";
        if ($this->getDado('exercicio'))
            $stSql .="      AND ld.exercicio = '".$this->getDado('exercicio')."'                    \n";
        if ($this->getDado('cod_entidade'))
            $stSql .="      AND ld.cod_entidade = ".$this->getDado('cod_entidade')."                \n";
        if ($this->getDado('cod_licitacao'))
            $stSql .="      AND ld.cod_licitacao = ".$this->getDado('cod_licitacao')."              \n";

        $stSql .="          AND  ld.cod_documento = pd.cod_documento                                \n";
        $stSql .="          AND  ld.cod_modalidade = pd.cod_modalidade                              \n";
        $stSql .="          AND  ld.cod_entidade = pd.cod_entidade                                  \n";
        $stSql .="          AND  ld.cod_licitacao = pd.cod_licitacao                                \n";

        return $stSql;
    }

    public function recuperaDocumentoUtilizado(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDocumentoUtilizado().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaDocumentoUtilizado()
    {
        $stSql =" select * ";
        $stSql.="   from licitacao.participante_documentos";
        $stSql.="  where cod_licitacao = ".$this->getDado('cod_licitacao')."        \n";
        $stSql.="    and cod_documento =".$this->getDado('cod_documento')."         \n";
        $stSql.="    and exercicio     = '".$this->getDado('exercicio')."'          \n";
        $stSql.="    and cod_entidade  = ".$this->getDado('cod_entidade')."         \n";
        $stSql.="    and cod_modalidade = ".$this->getDado('cod_modalidade')."      \n";

        return $stSql;
    }

}
