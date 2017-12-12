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
* Classe de mapeamento da tabela BENEFICIO.FORNECEDOR_VALE_TRANSPORTE
* Data de Criação: 08/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage mapeamento

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.06.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  BENEFICIO.FORNECEDOR_VALE_TRANSPORTE
  * Data de Criação: 07/07/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TBeneficioFornecedorValeTransporte extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TBeneficioFornecedorValeTransporte()
{
    parent::Persistente();
    $this->setTabela('beneficio.fornecedor_vale_transporte');

    $this->setCampoCod('fornecedor_numcgm');
    $this->setComplementoChave('');

    $this->AddCampo('fornecedor_numcgm','integer',true,'',true,true);

}

function montaRecuperaRelacionamento()
{
    $stSQL .= "SELECT fornecedor.*                                                  \n";
    $stSQL .= "     , sw_cgm.nom_cgm                                                \n";
    $stSQL .= "     , sw_cgm.numcgm                                                 \n";
    $stSQL .= "  FROM compras.fornecedor                                            \n";
    $stSQL .= "  JOIN sw_cgm_pessoa_juridica                                        \n";
    $stSQL .= "    ON sw_cgm_pessoa_juridica.numcgm = fornecedor.cgm_fornecedor     \n";
    $stSQL .= "  JOIN sw_cgm                                                        \n";
    $stSQL .= "    ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm                 \n";
    $stSQL .= " WHERE 1 = 1                                                         \n";

    return $stSQL;
}

function recuperaRelacionamentoRelatorio(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaRelacionamentoRelatorio().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoRelatorio()
{
    $stSQL .= "SELECT fornecedor.*                                                  \n";
    $stSQL .= "     , sw_cgm.nom_cgm                                                \n";
    $stSQL .= "     , sw_cgm.numcgm                                                 \n";
    $stSQL .= "  FROM compras.fornecedor                                            \n";
    $stSQL .= "  JOIN beneficio.vale_transporte                                     \n";
    $stSQL .= "    ON vale_transporte.fornecedor_vale_transporte_fornecedor_numcgm = fornecedor.cgm_fornecedor \n";
    $stSQL .= "  JOIN sw_cgm_pessoa_juridica                                        \n";
    $stSQL .= "    ON sw_cgm_pessoa_juridica.numcgm = fornecedor.cgm_fornecedor     \n";
    $stSQL .= "  JOIN sw_cgm                                                        \n";
    $stSQL .= "    ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm                 \n";
    $stSQL .= " WHERE 1 = 1                                                         \n";

    return $stSQL;
}

function validaExclusao($stFiltro = "", $boTransacao = "")
{
    $obErro = new erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql  = $this->montaValidaExclusao(). $this->getDado('fornecedor_numcgm')  .$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $rsRecordSet->getNumLinhas() > 0 ) {
            $obErro->setDescricao('Este fornecedor está ligado a um vale-transporte, por esse motivo não pode ser excluído!');
        }
    }

    return $obErro;
}

function montaValidaExclusao()
{
    $stSql = 'select * from beneficio.vale_transporte where fornecedor_vale_transporte_fornecedor_numcgm = ';

    return $stSql;
}

}
