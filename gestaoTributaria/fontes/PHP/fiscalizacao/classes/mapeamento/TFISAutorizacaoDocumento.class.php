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
    * Classe de regra de mapeamento para FISCALIZACAO.AUTORIZACAO_DOCUMENTO
    * Data de Criacao: 31/07/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

    * $Id: TAutorizacaoDocumento.class.php 29237 2008-04-16 12:02:48Z fabio $

    *Casos de uso: uc-05.07.04

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE                                                                      );

class TFISAutorizacaoDocumento extends Persistente
{

    /**
    * Metodo Construtor
    *
    */
    public function TFISAutorizacaoDocumento()
    {
        parent::Persistente();
        $this->setTabela('fiscalizacao.autorizacao_documento');

        $this->setCampoCod('cod_autorizacao,timestamp');
        $this->setComplementoChave('cod_autorizacao,cod_tipo_documento,cod_documento');

        $this->AddCampo( 'cod_autorizacao', 'integer', true, '', true, false );
        $this->AddCampo( 'cod_tipo_documento', 'integer', true, '', true, false );
        $this->AddCampo( 'cod_documento', 'integer', true, '', true, false );
        $this->AddCampo( 'timestamp', 'timestamp', false, '', true, false );
    }

    /**
     * Recupera os dados do Estabelecimento para AUTORIZACÃO DE IMPRESSÃO DE DOCUMENTOS FISCAIS.
     * Método utilizado por RFISEmitirAutorizacaoImpressao::emitirAutorizacao
     *
     * @param  RecordSet $rsRecordSet
     * @param  string    $stCriterio
     * @param  string    $stOrdem
     * @param  bool      $boTransacao
     * @return RecordSet
     * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
     */
    public function recuperaDadosEstabelecimento(&$rsRecordSet, $stCriterio, $stOrdem = '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql       = $this->montaRecuperaDadosEstabelecimento($stCriterio).$stOrdem;
        $obErro      = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    /**
     * Monta string SQL para o método recuperaDadosEstabelecimento
     *
     * @param  string $stCriterio
     * @return string
     * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
     */
    private function montaRecuperaDadosEstabelecimento($stCriterio)
    {
        $stSql  = " SELECT          cgm.numcgm                                                      \n";
        $stSql .= " 		        , cgm.nom_cgm                                                   \n";
        $stSql .= " 		        , cgm.logradouro                                                \n";
        $stSql .= " 		        , cgm.bairro                                                    \n";
        $stSql .= " 		        , cgm.cep                                                       \n";
        $stSql .= " 		        , swmu.nom_municipio                                            \n";
        $stSql .= " 		        , swuf.nom_uf                                                   \n";
        $stSql .= " 		        , publico.mascara_cpf_cnpj(swcpj.cnpj, 'cnpj') as cnpj          \n";
        $stSql .= " 		        , aeed.inscricao_economica                                      \n";
        $stSql .= " 		        , swcpj.insc_estadual                                           \n";
        $stSql .= " 		        , (select nom_cgm from sw_cgm as cgm_responsavel                \n";
        $stSql .= " 	               where cgm.cod_responsavel = cgm_responsavel.numcgm )         \n";
        $stSql .= "                    as nom_responsavel,                                          \n";
        $stSql .= "                    publico.mascara_cpf_cnpj(swcpf.cpf, 'cpf') as cpf            \n";
        $stSql .= "            FROM sw_cgm AS cgm                                                   \n";
        $stSql .= " INNER JOIN sw_uf AS swuf                                                        \n";
        $stSql .= "         ON swuf.cod_uf = cgm.cod_uf                                             \n";
        $stSql .= " INNER JOIN sw_municipio swmu				                                    \n";
        $stSql .= " 	    ON swmu.cod_municipio = cgm.cod_municipio						        \n";
        $stSql .= " 	   AND swmu.cod_uf = cgm.cod_uf                                             \n";
        $stSql .= "  LEFT JOIN sw_cgm_pessoa_juridica as swcpj							            \n";
        $stSql .= "         ON swcpj.numcgm = cgm.numcgm                                            \n";
        $stSql .= "  LEFT JOIN economico.sociedade as se                                            \n";
        $stSql .= "         ON se.numcgm = cgm.numcgm                                               \n";
        $stSql .= "  LEFT JOIN economico.cadastro_economico_empresa_direito AS aeed                 \n";
        $stSql .= "         ON aeed.inscricao_economica = se.inscricao_economica                    \n";
        $stSql .= "  LEFT JOIN sw_cgm_pessoa_fisica as swcpf                                        \n";
        $stSql .= "         ON cgm.cod_responsavel = swcpf.numcgm                                   \n";
        $stSql .= $stCriterio;

        return $stSql;
    }

}
?>
