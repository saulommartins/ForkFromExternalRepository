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
    * Classe de mapeamento da tabela ARRECADACAO.LOTE_ARQUIVO
    * Data de Criação: 04/05/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRLoteArquivo.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.4  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.3  2006/09/15 10:41:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.LOTE_INSCONSISTENCIA
  * Data de Criação: 04/05/2006

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Lucas Teixeira Stephanou

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRLoteArquivo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRLoteArquivo()
{
    parent::Persistente();
    $this->setTabela( "arrecadacao.lote_arquivo" );

    $this->setCampoCod('');
    $this->setComplementoChave('cod_lote,exercicio');

    $this->AddCampo('cod_lote'      ,'integer'  ,true,''    ,true,true);
    $this->AddCampo('exercicio'     ,'char'     ,true,'4'   ,true,true);
    $this->AddCampo('md5sum'        ,'varchar'  ,true,'32'  ,false,false);
    $this->AddCampo('header'        ,'varchar'  ,true,'150' ,false,false);
    $this->AddCampo('footer'        ,'varchar'  ,true,'150' ,false,false);
    $this->AddCampo('nom_arquivo'   ,'varchar'  ,true,'25'  ,false,false);
}

function recuperaVerificaArquivoMd5(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaVerificaArquivoMd5().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function recuperaVerificaArquivoConteudo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaVerificaArquivoConteudo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaVerificaArquivoMd5()
{
    $stSql   = " select md5sum                      \n";
    $stSql  .= "   from arrecadacao.lote_arquivo    \n";

    return $stSql;
}

function montaRecuperaVerificaArquivoConteudo()
{
    $stSql   = " select header, footer              \n";
    $stSql  .= "   from arrecadacao.lote_arquivo    \n";

    return $stSql;
}

}
?>
