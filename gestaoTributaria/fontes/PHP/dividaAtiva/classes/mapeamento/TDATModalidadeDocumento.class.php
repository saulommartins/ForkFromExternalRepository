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
    * Classe de mapeamento da tabela DIVIDA.MODALIDADE_DOCUMENTO
    * Data de Criação: 22/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATModalidadeDocumento.class.php 66396 2016-08-24 14:21:29Z evandro $

* Casos de uso: uc-05.04.07
*/

/*
$Log$
Revision 1.4  2007/07/20 20:55:11  cercato
correcao para exclusao de modalidade.

Revision 1.3  2007/02/09 18:28:54  cercato
correcoes para divida.cobranca

Revision 1.2  2006/10/05 15:01:22  dibueno
Alterações nas colunas da tabela

Revision 1.1  2006/09/25 14:54:56  cercato
classes de mapeamento para funcionamento da modalidade.

*/

include_once    ( CLA_PERSISTENTE );

class TDATModalidadeDocumento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATModalidadeDocumento()
    {
        parent::Persistente();
        $this->setTabela('divida.modalidade_documento');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_modalidade');

        $this->AddCampo('cod_modalidade','integer',true,'',true,true);
        $this->AddCampo('timestamp','timestamp',false,'',true,true);
        $this->AddCampo('cod_documento','integer',true,'',true,true);
        $this->AddCampo('cod_tipo_documento','integer',true,'',false,true);
    }

    public function recuperaListaDocumento(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaDocumento().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaDocumento()
    {
        $stSql  = " SELECT \n";
        $stSql .= "     dmd.cod_tipo_documento, \n";
        $stSql .= "     dmd.cod_documento, \n";
        $stSql .= "     amd.nome_documento \n";
        $stSql .= " FROM \n";
        $stSql .= "     divida.modalidade_documento AS dmd \n";
        $stSql .= " INNER JOIN \n";
        $stSql .= "     administracao.modelo_documento AS amd \n";
        $stSql .= " ON \n";
        $stSql .= "     amd.cod_documento = dmd.cod_documento \n";
        $stSql .= "     AND amd.cod_tipo_documento = dmd.cod_tipo_documento \n";

        return $stSql;
    }
    
    public function recuperaListaDocumentoModalidade(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stOrdem = empty($stOrdem) ? "ORDER BY nome_documento" : $stOrdem;
        $stSql = $this->montaRecuperaListaDocumentoModalidade().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaDocumentoModalidade()
    {
        $stSql = "
                SELECT DISTINCT
                       --modalidade_documento.cod_modalidade
                       modalidade_documento.cod_tipo_documento
                       ,modalidade_documento.cod_documento 
                       ,modelo_documento.nome_documento
                       ,modelo_documento.nome_arquivo_agt
                       ,arquivos_documento.nome_arquivo_swx
                FROM divida.modalidade_documento 
                INNER JOIN (SELECT cod_modalidade
                                   ,cod_documento
                                  ,max(timestamp) as  timestamp
                            FROM divida.modalidade_documento
                            group BY 1,2
                        ) as max
                    ON max.cod_modalidade = modalidade_documento.cod_modalidade
                    AND max.cod_documento = modalidade_documento.cod_documento
                INNER JOIN administracao.modelo_documento
                    ON modelo_documento.cod_documento = modalidade_documento.cod_documento
                    AND modelo_documento.cod_tipo_documento = modalidade_documento.cod_tipo_documento
                INNER JOIN administracao.modelo_arquivos_documento
                    ON modelo_arquivos_documento.cod_documento = modelo_documento.cod_documento
                    AND modelo_arquivos_documento.cod_tipo_documento = modelo_documento.cod_tipo_documento
                INNER JOIN administracao.arquivos_documento 
                    ON arquivos_documento.cod_arquivo = modelo_arquivos_documento.cod_arquivo
            ";
        return $stSql;
    }

}// end of class

?>
