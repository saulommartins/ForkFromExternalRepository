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
  * Classe de mapeamento da tabela ECONOMICO.LICENCA_DOCUMENTO
  * Data de Criação: 09/10/2006

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Diego Bueno Coelho

  * @package URBEM
  * @subpackage Mapeamento

  $Id: TCEMLicencaDocumento.class.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.02.12
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TCEMLicencaDocumento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    public function TCEMLicencaDocumento()
    {
        parent::Persistente();
        $this->setTabela('economico.licenca_documento');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_licenca, exercicio');

        $this->AddCampo('cod_licenca','integer',true,'',true,true);
        $this->AddCampo('exercicio','char',true,'4',true,true);
        $this->AddCampo('cod_tipo_documento','integer',true,'',true,true);
        $this->AddCampo('cod_documento','integer',true,'',true,true);

        $this->AddCampo('timestamp','timestamp',false,'',false,false);
        $this->AddCampo('num_alvara','integer',true,'',false,false);
    }

    public function buscaUltimoNumeroAlvara(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaBuscaUltimoNumeroAlvara().$stFiltro;
        $this->setDebug( $stSql );
        //$this->debug(); //exit;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaBuscaUltimoNumeroAlvara()
    {
        $stSql = "
            SELECT coalesce ( max(eld.num_alvara), 0 ) as valor
              FROM economico.licenca_documento as eld ";

        return $stSql;
    }

    public function listarLicencas(&$rsRecordSet , $stCondicao  , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListarLicencas().$stCondicao;
        $this->setDebug( $stSql );

        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListarLicencas()
    {
        $stSql = "
            SELECT *
              FROM economico.licenca_documento  ";

        return $stSql;
    }

    //utilizado no LSMAnterEmissao.php
    public function recuperaListaDocumentoLS(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaDocumentoLS().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );

        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaDocumentoLS()
    {
        $stSql  = " SELECT                                                                              \n";
        $stSql .= "     eld.*                                                                           \n";
        $stSql .= "     , amd.nome_documento                                                            \n";
        $stSql .= "     , amd.nome_arquivo_agt                                                          \n";
        $stSql .= "     , aad.nome_arquivo_swx                                                          \n";
        $stSql .= "     , (CASE WHEN ded.timestamp IS NOT NULL THEN                                     \n";
        $stSql .= "         to_char(ded.timestamp, 'dd/mm/YYYY')                                        \n";
        $stSql .= "        ELSE                                                                         \n";
        $stSql .= "         '&nbsp;'                                                                    \n";
        $stSql .= "        END                                                                          \n";
        $stSql .= "     ) as data_emissao                                                               \n";
        $stSql .= "     , ded.num_emissao                                                               \n";
        $stSql .= "     , coalesce (lca.inscricao_economica, lce.inscricao_economica)                   \n";
        $stSql .= "     as inscricao_economica                                                          \n";
        $stSql .= "     , lca.ocorrencia_licenca                                                        \n";
        $stSql .= " FROM                                                                                \n";
        $stSql .= "     economico.licenca_documento AS eld                                              \n";

        $stSql .= "     LEFT JOIN economico.licenca_atividade as lca                                    \n";
        $stSql .= "     ON lca.cod_licenca = eld.cod_licenca AND lca.exercicio = eld.exercicio          \n";

        $stSql .= "     LEFT JOIN economico.licenca_especial as lce                                     \n";
        $stSql .= "     ON lce.cod_licenca = eld.cod_licenca AND lce.exercicio = eld.exercicio          \n";

        $stSql .= "     INNER JOIN administracao.modelo_documento AS amd                                \n";
        $stSql .= "     ON  amd.cod_documento = eld.cod_documento                                       \n";
        $stSql .= "     AND amd.cod_tipo_documento = eld.cod_tipo_documento                             \n";

        $stSql .= "     INNER JOIN administracao.modelo_arquivos_documento AS amad                      \n";
        $stSql .= "     ON amad.cod_documento = eld.cod_documento                                       \n";
        $stSql .= "     AND amad.cod_tipo_documento = eld.cod_tipo_documento                            \n";

        $stSql .= "     INNER JOIN administracao.arquivos_documento AS aad                              \n";
        $stSql .= "     ON aad.cod_arquivo = amad.cod_arquivo                                           \n";

        $stSql .= "     LEFT JOIN                                                                       \n";
        $stSql .= "     ( SELECT                                                                        \n";
        $stSql .= "           tmp2.*                                                                    \n";
        $stSql .= "       FROM                                                                          \n";
        $stSql .= "       ( SELECT                                                                      \n";
        $stSql .= "           cod_licenca,                                                              \n";
        $stSql .= "           exercicio,                                                                \n";
        $stSql .= "           MAX(timestamp) AS timestamp                                               \n";
        $stSql .= "         FROM                                                                        \n";
        $stSql .= "           economico.emissao_documento                                               \n";
        $stSql .= "         GROUP BY                                                                    \n";
        $stSql .= "           cod_licenca, exercicio                                                    \n";
        $stSql .= "       ) AS tmp                                                                      \n";
        $stSql .= "       INNER JOIN economico.emissao_documento AS tmp2                                \n";
        $stSql .= "       ON    tmp.cod_licenca = tmp2.cod_licenca AND                                  \n";
        $stSql .= "       tmp.exercicio = tmp2.exercicio AND                                            \n";
        $stSql .= "       tmp.timestamp = tmp2.timestamp                                                \n";
        $stSql .= "     )AS ded                                                                         \n";
        $stSql .= "     ON ded.exercicio = eld.exercicio                                                \n";
        $stSql .= "     AND ded.cod_licenca = eld.cod_licenca                                           \n";
        $stSql .= "     AND ded.cod_tipo_documento = eld.cod_tipo_documento                             \n";
        $stSql .= "     AND ded.cod_documento = eld.cod_documento                                       \n";

        return $stSql;

    }
}
