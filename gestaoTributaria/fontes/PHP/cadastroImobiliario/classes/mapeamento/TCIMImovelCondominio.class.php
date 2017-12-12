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
     * Classe de mapeamento para a tabela IMOBILIARIO.IMOVEL_CONDOMINIO
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMImovelCondominio.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.6  2007/02/22 17:58:43  dibueno
Bug #8416#

Revision 1.5  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.IMOVEL_CONDOMINIO
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMImovelCondominio extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMImovelCondominio()
{
    parent::Persistente();
    $this->setTabela('imobiliario.imovel_condominio');

    $this->setCampoCod('');
    $this->setComplementoChave('inscricao_municipal,cod_condominio');

    $this->AddCampo('inscricao_municipal','integer',true,'',true,true);
    $this->AddCampo('cod_condominio','integer',true,'',true,true);

}

function listaImoveisCondominio(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

    $stSql = $this->montaListaImoveisCondominio( $stFiltro ).$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaListaImoveisCondominio($stFiltro)
{
    $stSQL .= " SELECT                                                                  \n";
    $stSQL .= "     imobiliario.fn_busca_imoveis_condominio( $stFiltro ) as valor       \n";

    return $stSQL;

}

}
