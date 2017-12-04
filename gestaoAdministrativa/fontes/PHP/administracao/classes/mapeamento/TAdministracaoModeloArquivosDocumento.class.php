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
    * Classe de mapeamento da tabela ADMINISTRACAO.MODELO_DOCUMENTO
    * Data de Criação: 22/02/2006

    * @author Analista:
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 20968 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-03-13 16:54:25 -0300 (Ter, 13 Mar 2007) $

    * Casos de uso: uc-01.03.100
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TAdministracaoModeloArquivosDocumento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAdministracaoModeloArquivosDocumento()
{
    parent::Persistente();
    $this->setTabela('ADMINISTRACAO.MODELO_ARQUIVOS_DOCUMENTO');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_acao,cod_documento,cod_arquivo, sistema');

    $this->AddCampo('cod_acao'          , 'integer' , true, ''  , true,  true  );
    $this->AddCampo('cod_documento'     , 'integer' , true, ''  , true,  true  );
    $this->AddCampo('cod_arquivo'       , 'integer' , true, ''  , true,  true  );
    $this->AddCampo('padrao'            , 'boolean' , true, ''  , false, false );
    $this->AddCampo('sistema'           , 'boolean' , true, ''  , false, false ,"false");
}
    public function recuperaDocumentos(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDocumentos().$stFiltro.$stOrdem;

        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDocumentos()
    {
        $stSQL  = " SELECT \n";
        $stSQL .= "     arquivo_modelo.* \n";
        $stSQL .= "     ,CASE WHEN arquivo_modelo.sistema = true then \n";
        $stSQL .= "         'modelos_sistema' \n";
        $stSQL .= "     else \n";
        $stSQL .= "         'modelos_usuario' \n";
        $stSQL .= "     end as dir \n";
        $stSQL .= "     ,modelo.nome_documento \n";
        $stSQL .= "     ,modelo.nome_arquivo_agt \n";
        //$stSQL .= "     ,arquivo.nome_arquivo_template \n";
        $stSQL .= " from \n";
        $stSQL .= "      administracao.modelo_arquivos_documento  arquivo_modelo \n";
        $stSQL .= "     ,administracao.modelo_documento as modelo \n";
        $stSQL .= "     ,administracao.arquivos_documento as arquivo \n";
        $stSQL .= " where \n";
        $stSQL .= "         arquivo_modelo.cod_acao = ".$this->getDado('cod_acao')." \n";
        $stSQL .= "     and arquivo_modelo.cod_documento = ".$this->getDado('cod_documento')." \n";
        $stSQL .= "     and arquivo_modelo.padrao = true \n";
        $stSQL .= "     and modelo.cod_documento = arquivo_modelo.cod_documento \n";
        $stSQL .= "     and arquivo.cod_arquivo = arquivo_modelo.cod_arquivo \n";

        return $stSQL;
    }

    public function recuperaListaDocumentos(&$rsRecordSet, $stAcao, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaDocumentos( $stAcao );
        $this->setDebug( $stSql );
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaDocumentos($stAcao)
    {
        $stSql  = " SELECT
                        modelo_arquivos_documento.cod_documento,
                        modelo_arquivos_documento.cod_tipo_documento

                    FROM
                        administracao.modelo_arquivos_documento

                    WHERE
                        modelo_arquivos_documento.cod_acao = ".$stAcao;

        return $stSql;
    }

} // end of class
