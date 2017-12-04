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
    * Classe de mapeamento da tabela compras.objeto
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 20958 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-03-13 12:52:28 -0300 (Ter, 13 Mar 2007) $

    * Casos de uso: uc-03.04.07
                    uc-03.05.26
*/

/*
$Log$
Revision 1.9  2007/03/13 15:52:21  hboaventura
Bug #8569#

Revision 1.8  2007/02/23 20:19:59  bruce
Bug #8184#
Bug #8262#

Revision 1.7  2006/12/19 12:46:18  bruce
colocação do UC de  julgamento de proposta

Revision 1.6  2006/11/09 17:59:41  fernando
função para recuperar o nome do objeto

Revision 1.5  2006/11/07 16:41:27  larocca
Inclusão dos Casos de Uso

Revision 1.4  2006/07/06 14:05:54  diego
Retirada tag de log com erro.

Revision 1.3  2006/07/06 12:11:10  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.objeto
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasObjeto extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TComprasObjeto()
{
    parent::Persistente();
    $this->setTabela("compras.objeto");

    $this->setCampoCod('cod_objeto');
    $this->setComplementoChave('');

    $this->AddCampo('cod_objeto','sequence',true,'',true,false);
    $this->AddCampo('descricao','text',true,'',false,false);
    $this->AddCampo('timestamp','TIMESTAMP',false,'',false,false);
}
    public function recuperaObjeto(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaObjeto().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }
    public function montaRecuperaObjeto()
    {
        $stSql  ="SELECT            \n";
        $stSql .="    cod_objeto    \n";
        $stSql .="    ,descricao    \n";
        $stSql .="FROM              \n";
        $stSql .="  compras.objeto  \n";
        if ($this->getDado('cod_objeto')) {
            $stSql .="where cod_objeto = ".$this->getDado('cod_objeto')."\n";
        }
        if ($this->getDado('descricao')) {
            $stSql .="where descricao = '".$this->getDado('descricao')."' \n";
        }

        return $stSql;

    }

}
