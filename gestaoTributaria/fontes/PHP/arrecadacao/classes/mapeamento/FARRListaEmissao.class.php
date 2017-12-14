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
    * Classe de mapeamento da fun��o ARRECADACAO.buscaLancamentosIE()
    * Data de Cria��o: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: FARRListaEmissao.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.02
*/

/*
$Log$
Revision 1.5  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.4  2006/09/15 10:40:57  fabio
corre��o do cabe�alho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Data de Cria��o: 12/05/2005

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Diego Bueno Coelho

  * @package URBEM
  * @subpackage Mapeamento
*/
class FARRListaEmissao extends Persistente
{
    public $inExercicio    ;
    public $stNumAnterior  ;
    public $inCodGrupo     ;
    public $inCodCredito   ;
    public $inCodEspecie   ;
    public $inCodGenero    ;
    public $inCodNatureza  ;
    public $inCgmInicial   ;
    public $inCgmFinal     ;
    public $inCodIIInicial ;
    public $inCodIIFinal   ;
    public $inCodIEInicial ;
    public $inCodIEFinal   ;
    public $stLocInicial;
    public $stLocFinal;

/**
    * M�todo Construtor
    * @access Private
*/
function FARRListaEmissao()
{
    parent::Persistente();
    $this->AddCampo('valor','varchar'  ,false       ,''     ,false   ,false );
    $this->inExercicio    = '0';
    $this->stNumAnterior  = '';
    $this->inCodGrupo     = '0';
    $this->inCodCredito   = '0';
    $this->inCodEspecie   = '0';
    $this->inCodGenero    = '0';
    $this->inCodNatureza  = '0';
    $this->inCgmInicial   = '0';
    $this->inCgmFinal     = '0';
    $this->inCodIIInicial = '0';
    $this->inCodIIFinal   = '0';
    $this->inCodIEInicial = '0';
    $this->inCodIEFinal   = '0';
    $this->stLocInicial   = '';
    $this->stLocFinal     = '';
}

function executaFuncao(&$rsRecordset, $stParametros,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;

    $stSql  = $this->montaExecutaFuncao();
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL($rsRecordset,$stSql, $boTransacao );

return $obErro;
}

function montaExecutaFuncao()
{
    $stSql  = " SELECT                                          \r\n";
    $stSql .= "        *                                             \r\n";
    $stSql .= " FROM arrecadacao.fn_lista_emissao(".$this->inExercicio.",'".$this->stNumAnterior."',".$this->inCodGrupo.",".$this->inCodCredito.",".$this->inCodEspecie.",".$this->inCodGenero.",".$this->inCodNatureza.",".$this->inCgmInicial.",".$this->inCgmFinal.",".$this->inCodIIInicial.",".$this->inCodIIFinal.",".$this->inCodIEInicial.",".$this->inCodIEFinal.",'".$this->stLocInicial."', '".$this->stLocFinal."' )    \r\n";
    $stSql .= " as lista_emissao (                          \r\n";
    $stSql .= " numeracao varchar,                       \r\n";
    $stSql .= " impresso  boolean,                         \r\n";
    $stSql .= " exercicio int,                                   \r\n";
    $stSql .= " exercicio_calculo int,                                   \r\n";
    $stSql .= " cod_convenio int,                           \r\n";
    $stSql .= " cod_carteira int,                             \r\n";
    $stSql .= " convenio_atual int,                         \r\n";
    $stSql .= " carteira_atual int,                           \r\n";
    $stSql .= " cod_parcela int,                              \r\n";
    $stSql .= " nr_parcela int,                                 \r\n";
    $stSql .= " info_parcela varchar,                       \r\n";
    $stSql .= " vencimento_parcela date,               \r\n";
    $stSql .= " vencimento_parcela_br varchar,      \r\n";
    $stSql .= " vencimento_original date,               \r\n";
    $stSql .= " vencimento_original_br varchar,      \r\n";
    $stSql .= " valor_parcela numeric,                    \r\n";
    $stSql .= " cod_lancamento int,                        \r\n";
    $stSql .= " vencimento_lancamento date,        \r\n";
    $stSql .= " valor_lancamento numeric,             \r\n";
    $stSql .= " numcgm varchar,                                    \r\n";
    $stSql .= " nom_cgm varchar,                           \r\n";
    $stSql .= " vinculo varchar,                               \r\n";
    $stSql .= " id_vinculo varchar,                          \r\n";
    $stSql .= " chave_vinculo varchar,                    \r\n";
    $stSql .= " inscricao int                             \r\n";
    $stSql .= "                  )                                     \r\n";

return $stSql;
}

}
?>
