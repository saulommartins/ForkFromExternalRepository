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
    * Classe de mapeamento da tabela tcemg.tipo_doc_credor
    * Data de Criação: 19/05/2014

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Evandro Noguez Melos

    * @package URBEM
    * @subpackage Mapeamento
    $Id: TTCEMGTipoDocumentoCredor.class.php 59719 2014-09-08 15:00:53Z franver $
*/
/*
$Log$

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGTipoDocumentoCredor extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCEMGTipoDocumentoCredor()
{
    parent::Persistente();
    $this->setTabela("tcemg.tipo_doc_credor");

    $this->setCampoCod('codigo');
    $this->setComplementoChave('');

    $this->AddCampo('codigo'        ,'integer'  ,true   ,''     ,true  ,false);
    $this->AddCampo('descricao'     ,'varchar'  ,false  ,'40'   ,false ,false);

}

function recuperaDocumentosCredor(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDocumentosCredor().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaDocumentosCredor()
{
    $stSql = " SELECT * 
               FROM tcemg.tipo_doc_credor
            ";
    return $stSql;
}

function recuperaComboDocumentosCredor(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaComboDocumentosCredor().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaComboDocumentosCredor()
{
    $stSql = "  SELECT   *
                FROM tcemg.tipo_doc_credor
                LEFT JOIN tcemg.de_para_documento
                    ON tipo_doc_credor.codigo = de_para_documento.cod_doc_tce
                LEFT JOIN licitacao.documento
                    ON documento.cod_documento = de_para_documento.cod_doc_urbem        
            ";
    return $stSql;
}

public function __destruct(){}


}//fim classe
?>