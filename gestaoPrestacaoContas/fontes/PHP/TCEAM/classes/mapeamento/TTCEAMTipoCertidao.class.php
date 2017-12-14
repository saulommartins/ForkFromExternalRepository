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
    * Classe de mapeamento da tabela tceam.tipo_certidao
    * Data de Criação: 19/05/2014

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Evandro Noguez Melos

    * @package URBEM
    * @subpackage Mapeamento
    $Id: TTCEAMTipoCertidao.class.php 59612 2014-09-02 12:00:51Z gelson $
*/
/*
$Log$

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEAMTipoCertidao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCEAMTipoCertidao()
{
    parent::Persistente();
    $this->setTabela("tceam.tipo_certidao");

    $this->setCampoCod('cod_tipo_certidao');
    $this->setComplementoChave('');

    $this->AddCampo('cod_tipo_certidao' ,'integer'  ,true   ,''     ,true  ,false);
    $this->AddCampo('descricao'         ,'varchar'  ,true   ,'80'   ,false ,false);

}

function recuperaDocumentosCertidao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDocumentosCertidao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaDocumentosCertidao()
{
    $stSql = " SELECT * 
               FROM tceam.tipo_certidao
            ";
    return $stSql;
}

function recuperaComboDocumentosCertidao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaComboDocumentosCertidao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaComboDocumentosCertidao()
{
    $stSql = "  SELECT   *
                FROM tceam.tipo_certidao              
                LEFT JOIN tceam.tipo_certidao_documento
                    ON tipo_certidao_documento.cod_tipo_certidao = tipo_certidao.cod_tipo_certidao
                LEFT JOIN licitacao.documento
                    ON documento.cod_documento = tipo_certidao_documento.cod_documento                
            ";
    return $stSql;
}

}//fim classe
?>