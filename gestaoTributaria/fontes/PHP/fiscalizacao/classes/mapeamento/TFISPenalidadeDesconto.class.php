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
 * Classe de mapeamento para FISCALIZACAO.PENALIDADE_DESCONTO
 * Data de Criação: 26/08/2008
 *
 *
 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Janilson Mendes P. da Silva
 *
 * @package URBEM
 * @subpackage Mapeamento

 $Id:

 * Caso de uso:
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once( CLA_PERSISTENTE );

class TFISPenalidadeDesconto extends Persistente
{

    /**
     * Método construtor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTabela( 'fiscalizacao.penalidade_desconto' );

        $this->setCampoCod( 'cod_penalidade' );
        $this->setComplementoChave( 'cod_desconto' );

        $this->addCampo( 'cod_penalidade', 'integer', true, '', true, true );
        $this->addCampo( 'cod_desconto', 'integer', true, '', true, false );
        $this->addCampo( 'prazo', 'integer', true, '', false, false );
        $this->addCampo( 'desconto', 'numeric', true, '14,2', false, false );
    }

    public function recuperaPenalidadeDesconto(&$rsRecordSet, $stCondicao, $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro();
        $obConexao   = new Conexao();
        $rsRecordSet = new RecordSet();

        $stSQL = $this->montaRecuperaPenalidadeDesconto( $stCondicao ) . $stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSQL, $boTransacao );

        return $obErro;
    }

    private function montaRecuperaPenalidadeDesconto($stCondicao)
    {
        if ($stCondicao) {
            $stCondicao = " WHERE " . $stCondicao;
        }

        $stSQL  = " SELECT cod_penalidade                                   \n";
        $stSQL .= "       ,cod_desconto                                     \n";
        $stSQL .= "       ,prazo                                       	    \n";
        $stSQL .= "       ,desconto                                   	    \n";
        $stSQL .= " FROM fiscalizacao.penalidade_desconto                   \n";
        $stSQL .= $stCondicao;

        return $stSQL;
    }
}

?>
