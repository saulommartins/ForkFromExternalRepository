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

    $Revision: 18160 $
    $Name$
    $Author: tonismar $
    $Date: 2006-11-24 15:50:12 -0200 (Sex, 24 Nov 2006) $

    * Casos de uso: uc-01.03.100
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TAdministracaoModeloDocumento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAdministracaoModeloDocumento()
{
    parent::Persistente();
    $this->setTabela('ADMINISTRACAO.MODELO_DOCUMENTO');

    $this->setCampoCod('cod_documento');
    $this->setComplementoChave('');

    $this->AddCampo('cod_documento'     , 'integer' , true, ''   , true , false );
    $this->AddCampo('nome_documento'    , 'varchar' , true, '100', false, false );
    $this->AddCampo('nome_arquivo_agt'   , 'varchar' , true, '100', false, false );
    $this->AddCampo('cod_tipo_documento', 'integer' , true, ''   , true , true );
}
    // PARA MONTAR O combo, somente pega o padrao
    public function montaRecuperaRelacionamento()
    {
        $stSql  = "           SELECT a.padrao                                              \n";
        $stSql .= "             , a.cod_acao                                               \n";
        $stSql .= "             , b.cod_documento                                          \n";
        $stSql .= "             , b.cod_tipo_documento                                     \n";
        $stSql .= "             , b.nome_documento                                         \n";
        $stSql .= "             , b.nome_arquivo_agt                                       \n";
        $stSql .= "          FROM administracao.modelo_arquivos_documento a                \n";
        $stSql .= "    INNER JOIN administracao.modelo_documento b                         \n";
        $stSql .= "            ON b.cod_documento = a.cod_documento                        \n";
        //$stSql .= "           AND a.padrao = true                                          \n";
        return $stSql;
    }

    public function recuperaArquivosDocumentoAcao(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaArquivosDocumentoAcao().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    public function montaRecuperaArquivosDocumentoAcao()
    {
        $stSql  = "           SELECT a.padrao                                              \n";
        $stSql .= "             , a.cod_acao                                               \n";
        $stSql .= "             , a.padrao                                                 \n";
        $stSql .= "             , b.cod_documento                                          \n";
        $stSql .= "             , b.nome_documento                                         \n";
        $stSql .= "             , b.nome_arquivo_agt                                       \n";
        $stSql .= "             , c.cod_arquivo                                            \n";
        $stSql .= "             , c.nome_arquivo_template                                  \n";
        $stSql .= "             , c.checksum                                               \n";
        $stSql .= "             , c.sistema                                                \n";
        $stSql .= "             , CASE                                                     \n";
        $stSql .= "                 WHEN c.sistema = true then 'modelos_sistema'           \n";
        $stSql .= "                 ELSE 'modelos_usuario'                                 \n";
        $stSql .= "               END AS dir                                               \n";
        $stSql .= "          FROM administracao.modelo_arquivos_documento a                \n";
        $stSql .= "    INNER JOIN administracao.modelo_documento b                         \n";
        $stSql .= "            ON b.cod_documento = a.cod_documento                        \n";
        $stSql .= "    INNER JOIN administracao.arquivos_documento c                       \n";
        $stSql .= "            ON c.cod_arquivo = a.cod_arquivo                            \n";
        $stSql .= "           AND c.sistema     = a.sistema                                \n";

        return $stSql;
    }

} // end of class
