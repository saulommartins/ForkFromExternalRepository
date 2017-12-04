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
    * Classe de mapeamento da tabela ARRECADACAO.DOCUMENTO_EMISSAO
    * Data de Criação: 04/10/2007

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRDocumentoEmissao.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.1  2007/10/09 18:47:26  cercato
 Ticket#9281#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TARRDocumentoEmissao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TARRDocumentoEmissao()
    {
        parent::Persistente();
        $this->setTabela('arrecadacao.documento_emissao');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_documento,num_documento,exercicio');

        $this->AddCampo('cod_documento','integer',true,'',true,true);
        $this->AddCampo('num_documento','integer',true,'',true,false);
        $this->AddCampo('exercicio','varchar',true,'4',true,false);
        $this->AddCampo('numcgm','integer',true,'',false,true);
        $this->AddCampo('timestamp','timestamp',false,'',false,false);
    }

    public function recuperaProxNumDocumento(&$rsRecordSet, $stCodDocumento, $stExercicio, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql  = $this->montaRecuperaProxNumDocumento( $stCodDocumento, $stExercicio );
        $this->setDebug($stSql);
        #$this->debug(); exit;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaProxNumDocumento($stCodDocumento, $stExercicio)
    {
        $stSql = " SELECT
                        max(documento_emissao.num_documento) AS numdoc
                   FROM
                        arrecadacao.documento_emissao
                   WHERE
                        documento_emissao.cod_documento = ".$stCodDocumento." AND documento_emissao.exercicio = '".$stExercicio."'";

        return $stSql;
    }
}
?>
