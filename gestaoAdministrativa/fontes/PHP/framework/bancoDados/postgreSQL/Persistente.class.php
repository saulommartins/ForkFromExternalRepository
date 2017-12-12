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
    * Classe de Persistência com banco de dados
    * Data de Criação   : 05/02/2004

    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package Conectividade
    * @subpackage Persistente

    $Id: Persistente.class.php 66267 2016-08-04 14:33:27Z evandro $

    Casos de uso: uc-01.01.00

*/

/**
    * Classe de persistênsia que executa as querys mais comuns dinamicamente no banco de dados
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Diego Barbosa Victoria
*/
class Persistente extends Objeto
{
/**
    * @var String
    * @access Private
*/
var $stTabela;
/**
    * @var String
    * @access Private
*/
var $stCampoCod;
/**
    * @var String
    * @access Private
*/
var $stSequence;
/**
    * @var String
    * @access Private
*/
var $stComplementoChave;
/**
    * @var Array
    * @access Private
*/
var $arEstrutura;
/**
    * @var Array
    * @access Private
*/
var $arEstruturaAuxiliar;

/**
    * @access Public
    * @param String $valor
*/
function setTabela($valor) { $this->stTabela           = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCampoCod($valor) { $this->stCampoCod         = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setSequence($valor) { $this->stSequence         = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setComplementoChave($valor) { $this->stComplementoChave = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setEstrutura($valor) { $this->arEstrutura        = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setEstruturaAuxiliar($valor) { $this->arEstruturaAuxiliar= $valor; }

/**
    * @access Public
    * @return String
*/
function getTabela() { return $this->stTabela;                        }
/**
    * @access Public
    * @return String
*/
function getCampoCod() { return $this->stCampoCod;                      }
/**
    * @access Public
    * @return String
*/
function getSequence() { return $this->stSequence;                      }
/**
    * @access Public
    * @return String
*/
function getComplementoChave() { return $this->stComplementoChave;              }
/**
    * @access Public
    * @return Array
*/
function GetEstrutura() { return $this->arEstrutura;                     }
/**
    * @access Public
    * @return Array
*/
function GetEstruturaAuxiliar() { return $this->arEstruturaAuxiliar;     }

/**
    * Método Construtor
    * @access Private
*/
function Persistente()
{
    parent::Objeto();
    $this->setEstrutura        ( array() );
    $this->setEstruturaAuxiliar( array() );
}

/**
    * Gera o relacionamento de Chave Estrangeira para outra classe de mapeamento,
    * gerando possível navegabilidade entre os objetos de mapeamento.
    * @access Private
    * @param String  $stForeignKey
    * @param String  $stCampoForeignKey
*/
function geraForeignKey($stForeignKey,$stCampoForeignKey)
{
    include_once(constant(strtoupper(substr($stForeignKey,0,4))).'/'.$stForeignKey.'.class.php');

    $refClass = new ReflectionObject($this);

    if ( !$refClass->hasProperty('ob'.$stForeignKey) ) {
        $this->{'ob'.$stForeignKey} = new $stForeignKey;
    }
}

/**
    * Adiciona campos na estrutura da Persistente.
    * Cada vez que este método é chamado é adicionado um novo campo na estrutura.
    * @access Public
    * @param String  $stNome
    * @param String  $stTipo
    * @param Boolean $boRequerido
    * @param Numeric $nrTamanho
    * @param Boolean $boPrimaryKey
    * @param Boolean $boForeignKey
    * @param String  $stConteudo
*/
//function AddCampo($stNome,$stTipo,$boRequerido='', $nrTamanho='',$boPrimaryKey='',$boForeignKey='',$stConteudo = '') {
function AddCampo($stNome,$stTipo,$boRequerido='', $nrTamanho='',$boPrimaryKey='',$stForeignKey='',$stCampoForeignKey='',$stConteudo=null)
{
    $obCampo = new CampoTabela;
    $obCampo->setNomeCampo      ($stNome);
    $obCampo->setTipoCampo      ($stTipo);
    $obCampo->setTamanho        ($nrTamanho);
    $obCampo->setRequerido      ($boRequerido);
    $obCampo->setPrimaryKey     ($boPrimaryKey);
    $obCampo->setForeignKey     ($stForeignKey);

    if ($stTipo == "AUXILIAR") {
        $obCampo->setConteudo       ($stConteudo);
        array_push($this->arEstruturaAuxiliar,$obCampo);
    } else {
        if ($stForeignKey && $stForeignKey !== true) {
            $this->geraForeignKey($stForeignKey,$stCampoForeignKey);
            $obCampo->setCampoForeignKey( ((trim($stCampoForeignKey)!='')?$stCampoForeignKey:$stNome) );
            array_push($this->arEstrutura,$obCampo);

            $this->setDado( $stNome, $stConteudo );

        } else {
            $obCampo->setConteudo       ($stConteudo);

            if ( true == $boRequerido && ($stTipo == 'varchar' || $stTipo == 'text' )  && is_null($stConteudo) ) {
                $obCampo->setConteudo('');
            }

            array_push($this->arEstrutura,$obCampo);
        }
    }
}

/**
    * Seta o dado indicado pelo parâmetro $stNomeCampo com o valor $stValor
    * Desta maneira, é carregado o objeto Persistente com o determinado valor.
    * @access Public
    * @param String  $stNomeCampo
    * @param Mixed   $stValor
*/
function setDado($stNomeCampo , $stValor)
{
    $inCont = 0;

    foreach ($this->getEstrutura() as $obCampo) {
        if ($stNomeCampo == $obCampo->getNomeCampo()) {

            $arTiposString = array('varchar','text','char');

            if ($stValor === '' && !in_array($obCampo->getTipoCampo(),$arTiposString)) {
                $stValor = null;
            }

            $this->arEstrutura[$inCont]->setConteudo($stValor);

            $class_var_fk = 'ob'.$obCampo->getForeignKey();

            if ( $obCampo->getCampoForeignKey() && isset($this->{$class_var_fk}) && is_object($this->{$class_var_fk}) ) {
                $this->{'ob'.$obCampo->getForeignKey()}->setDado( $obCampo->getCampoForeignKey() , $stValor );
            }

            return true;
        }
        $inCont++;
    }
    $inCont = 0;
    foreach ($this->getEstruturaAuxiliar() as $obCampo) {
        if ($stNomeCampo == $obCampo->getNomeCampo()) {
            $this->arEstruturaAuxiliar[$inCont]->setConteudo($stValor);

            return true;
        }
        $inCont++;
    }
    //Caso não tenha encontrado o campo na Estrutura nem na EstruturaAuxiliar, adiciona o campo na Estrutura Auxiliar
    $this->AddCampo($stNomeCampo,'AUXILIAR','','','','','',$stValor);
}
/**
    * Recupera o dado indicado pelo parâmetro $stNomeCampo
    * Desta maneira, é retornado do objeto Persistente o determinado valor.
    * @access Public
    * @param  String $stNomeCampo
    * @return Mixed  $stValor
*/
function getDado($stNomeCampo)
{
    $inCont = 0;
    foreach ($this->getEstrutura() as $obCampo) {
        if ($stNomeCampo == $obCampo->getNomeCampo()) {
            if ( $obCampo->getCampoForeignKey() && is_object($this->{'ob'.$obCampo->getForeignKey()}) ) {
                return $this->{'ob'.$obCampo->getForeignKey()}->getDado( $obCampo->getCampoForeignKey() );
            }

            return $obCampo->getConteudo();
        }
        $inCont++;
    }
    $inCont = 0;
    foreach ($this->getEstruturaAuxiliar() as $obCampo) {
        if ($stNomeCampo == $obCampo->getNomeCampo()) {
            return $obCampo->getConteudo();
        }
        $inCont++;
    }
}

/**
    * Seta os dados necessários à auditoria e efetua a inclusão no banco de dados.
    * @access Private
    * @param  Boolean $boTransacao
*/
function montaAuditoria($boTransacao)
{
    if (strtolower(get_class($this)) != 'tauditoria') {
        $stValor = $this->obAuditoria->getDado('objeto');
        for ($inCount=0; $inCount<strlen($stValor); $inCount++) {
            if ($stValor[ $inCount ] == '[') $inInicial = $inCount;
            if (($stValor[ $inCount ] == ']') && ($inInicial)) {
                $stOut .= $this->getDado( trim( substr($stValor,$inInicial+1,(($inCount-$inInicial)-1)) ) );
                $inInicial = false;
            }elseif(!$inInicial)
                $stOut .= $stValor[ $inCount ];
        }

        $this->obAuditoria->setDado('objeto',$stOut);

        $arMicroTime = explode(" ", microtime() );
        $stData = date("Y-m-d H:m:", $arMicroTime[1] );//2004-03-03 09:44:10.22
        $inMinuto = date("s", $arMicroTime[1] );
        $stMinutos = $inMinuto + $arMicroTime[0];
        if ($inMinuto < 10) {
            $stMinutos = "0".$stMinutos;
        }
        $stTimeStamp = $stData.substr($stMinutos, 0, 6 );
        $this->obAuditoria->setDado('timestamp', $stTimeStamp );
        $this->obAuditoria->inclusao( $boTransacao );
    }
}
/**
    * Efetua a inclusão no banco de dados a partir do comando DML montado no método montaInclusao.
    * @access Public
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function inclusao($boTransacao = "")
{
    $stFiltro   = "";
    $obErro     = new Erro;
    $obConexao  = new Transacao;//Conexao;

    # Método nem sempre declarado na classe que estende a Persistente.
    if (is_object($this) && is_subclass_of($this, "Persistente")) {
        if (method_exists($this, 'validaInclusao')) {
            $obErro = $this->validaInclusao ($stFiltro, $boTransacao);
        }
    }

    if ( !$obErro->ocorreu() ) {
        $stSql = $this->montaInclusao( $boTransacao, $arBlob, $obConexao );
        $this->setDebug( 'inclusao' );

        if ($arBlob["qtd_blob"]) {
            $boTranFalse = false;

            if ( !Sessao::getTrataExcecao() && !$boTransacao) {
                $boTransacao = true;
                $boTranFalse = true;
            }

            $obErro = $obConexao->executaDML( $stSql, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                for ($inX=0; $inX<$arBlob["qtd_blob"]; $inX++) {
                    $obConexao->gravaBlob( $arBlob["blob_oid"][$inX], $arBlob["blob"][$inX] );
                }

                if ($boTranFalse) {
                    $obConexao->fechaTransacao( $boTranFalse, $boTransacao, $obErro);
                }
            }
        } else {
            $obErro = $obConexao->executaDML( $stSql, $boTransacao );

            $boSalvaAuditoria = false;

            //Caso não ocorra erro, inclui um detalhe da Auditoria.
            if (!$obErro->ocorreu() && !in_array(get_class($this), array("TAuditoriaDetalhe", "TAuditoria"))) {
                $obAuditoria = Sessao::read('obAuditoria'); //recupera da Sessão.

                # Caso o objeto de Auditoria não esteja na sessão, é porque não existe uma transação aberta.
                # Então é criado o objeto de Auditoria para ser inserido na base.
                if (is_null($obAuditoria)) {
                    $obAuditoria = new Auditoria();
                    $boSalvaAuditoria = true;
                }

                # Adiciona os detalhes ao objeto de Auditoria
                $obAuditoria->adicionarDetalhe($this);

                # Caso a inserção não esteja dentro de uma Transação, salva na Auditoria o registro inserido.
                if ($boSalvaAuditoria === true) {
                    $obAuditoria->salvar($this, false);
                } else {
                    # Mantém o objeto de Auditoria com o detalhe adicionado, pois esse só será incluido quando a Transação for encerrada.
                    Sessao::write('obAuditoria', $obAuditoria);
                }

            }
        }
    }

    return $obErro;
}

/**
    * Efetua a exclusão no banco de dados a partir do comando DML montado no método montaExclusao.
    * @access Public
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function excluirTodos($boTransacao = "")
{
    $obErro     = new Erro;
    $obConexao  = new Conexao;
    $this->setDebug( 'exclusao' );

    $obErro = $this->validaExclusao( $stFiltro,$boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stSql = $this->montaExclusao();
        $obErro = $obConexao->executaDML( $stSql, $boTransacao );
    }

    return $obErro;
}

/**
    * Efetua a exclusão no banco de dados a partir do comando DML montado no método montaExclusao.
    * @access Public
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function exclusao($boTransacao = "")
{
    $obErro     = new Erro;
    $obConexao  = new Conexao;
    $this->setDebug( 'exclusao' );

    if( empty($stFiltro) )
        $stFiltro = "";

    # Método nem sempre declarado na classe que estende a Persistente.
    if (is_object($this) && is_subclass_of($this, "Persistente")) {
        if (method_exists($this, 'validaExclusao')) {
            $obErro = $this->validaExclusao ($stFiltro, $boTransacao);
        }
    }

    if ( !$obErro->ocorreu() ) {
        $stSql = $this->montaExclusao();
        $stChave = $this->montaChave();
        if ($stChave) {
            $stSql .= " WHERE ".$stChave;
            $obErro = $obConexao->executaDML( $stSql, $boTransacao );
            //Caso não ocorra erro, inclui um detalhe da Auditoria. TESTE
            if (!$obErro->ocorreu() && !in_array(get_class($this), array("TAuditoriaDetalhe", "TAuditoria"))) {
                $obAuditoria = Sessao::read('obAuditoria'); //recupera da Sessão.

                # Caso o objeto de Auditoria não esteja na sessão, é porque não existe uma transação aberta.
                # Então é criado o objeto de Auditoria para ser inserido na base.
                if (is_null($obAuditoria)) {
                    $obAuditoria = new Auditoria();
                    $boSalvaAuditoria = true;
                }

                # Adiciona os detalhes ao objeto de Auditoria
                $obAuditoria->adicionarDetalhe($this);

                # Caso a inserção não esteja dentro de uma Transação, salva na Auditoria o registro inserido.
                if ($boSalvaAuditoria === true) {
                    $obAuditoria->salvar($this, false);
                } else {
                    # Mantém o objeto de Auditoria com o detalhe adicionado, pois esse só será incluido quando a Transação for encerrada.
                    Sessao::write('obAuditoria', $obAuditoria);
                }

            }
            // fim da parte da auditoria, TESTE
        } else {
            $obErro->setDescricao( "Na classe persistente deve ser setada a chave!<br>\n" );
        }
    }

    return $obErro;
}
/**
    * Efetua a alteração de registros no banco de dados a partir do comando DML montado no método montaAlteracao.
    * @access Public
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function alteracao($boTransacao = "")
{
    $stFiltro   = "";
    $obErro     = new Erro;
    $obConexao  = new Transacao;
    $this->setDebug( 'alteracao' );

    # Método nem sempre declarado na classe que estende a Persistente.
    if (is_object($this) && is_subclass_of($this, "Persistente")) {
        if (method_exists($this, 'validaAlteracao')) {
            $obErro = $this->validaAlteracao ($stFiltro, $boTransacao);
        }
    }

    if ( !$obErro->ocorreu() ) {
        $stSql = $this->montaAlteracao( $arBlob, $obConexao, $boTransacao);
        $stChave = $this->montaChave();

        if ($stChave) {
            $stSql .= " WHERE ".$stChave;

            if ($arBlob["qtd_blob"]) {
                $boTranFalse = false;
                if ( !Sessao::getTrataExcecao() && !$boTransacao) {
                    $boTransacao = true;
                    $boTranFalse = true;
                }

                //salva na sessão para poder ser feito corretamente a comparação na auditoria
                $stSqlAux = "SELECT * FROM ".$this->getTabela()." WHERE ".$stChave;
                $obErro = $obConexao->executaSQL( $rsRecordSet, $stSqlAux, $boTransacao);

                if (!$obErro->ocorreu()) {
                    Sessao::write('rsAltera'.$this->getTabela().'', $rsRecordSet);
                }

                $obErro = $obConexao->executaDML( $stSql, $boTransacao );

                if ( !$obErro->ocorreu() ) {
                    for ($inX=0; $inX<$arBlob["qtd_blob"]; $inX++) {
                        $obConexao->gravaBlob( $arBlob["blob_oid"][$inX], $arBlob["blob"][$inX] );
                    }

                    if ($boTranFalse) {
                        $obConexao->fechaTransacao( $boTranFalse, $boTransacao, $obErro );
                    }
                }
            } else { //salva na sessão para poder ser feito corretamente a comparação na auditoria, pois na auditoria acaba sempre pegando o valor já alterado
                $stSqlAux = "SELECT * FROM ".$this->getTabela()." WHERE ".$stChave;
                $obErro = $obConexao->executaSQL( $rsRecordSet, $stSqlAux, $boTransacao);

                $rsRecordSessao = new RecordSet;

                // Essa parte agora acrescenta todas as alterações consecutivas de uma tabela para não ocorrer sobrescrita de dados na auditoria
                if (!$obErro->ocorreu()) {
                    if (Sessao::read('rsAltera_'.$this->getTabela())) {
                        $rsSessao = Sessao::read('rsAltera_'.$this->getTabela());
                        $arSessao = $rsSessao->arElementos;

                        $rsRecordSessao->preenche( $arSessao );
                        $rsRecordSessao->add( $rsRecordSet->arElementos[0] );
                        Sessao::write('rsAltera_'.$this->getTabela(), $rsRecordSessao);
                    } else {
                         Sessao::write('rsAltera_'.$this->getTabela(), $rsRecordSet);
                    }
                }

                $obErro = $obConexao->executaDML( $stSql, $boTransacao );

                //Caso não ocorra erro, inclui um detalhe da Auditoria.
                if (!$obErro->ocorreu() && !in_array(get_class($this), array("TAuditoriaDetalhe", "TAuditoria"))) {
                    $obAuditoria = Sessao::read('obAuditoria'); //recupera da Sessão.

                    # Caso o objeto de Auditoria não esteja na sessão, é porque não existe uma transação aberta.
                    # Então é criado o objeto de Auditoria para ser inserido na base.
                    if (is_null($obAuditoria)) {
                        $obAuditoria = new Auditoria();
                        $boSalvaAuditoria = true;
                    }

                    # Adiciona os detalhes ao objeto de Auditoria
                    $obAuditoria->adicionarDetalhe($this);

                    # Caso a inserção não esteja dentro de uma Transação, salva na Auditoria o registro inserido.
                    if ($boSalvaAuditoria === true) {
                        $obAuditoria->salvar($this, false);
                    } else {
                        # Mantém o objeto de Auditoria com o detalhe adicionado, pois esse só será incluido quando a Transação for encerrada.
                        Sessao::write('obAuditoria', $obAuditoria);
                    }
                }
            }
        } else {
            $obErro->setDescricao( "Na classe persistente deve ser setada a chave!<br>\n" );
        }
    }

    return $obErro;
}
/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaTodos.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaTodos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaTodos().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaChave.
    * Este método é responsável por criar os filtros a partir dos valores informados na Persistente da tabela específica.
    * OS métodos disponíveis para filtro são: setCampoCod e setComplementoChave.
    * @access Public
    * @param  Integer $rsRecordSet Objeto RecordSet
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaPorChave(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaPorChave();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function consultar($boTransacao = "")
{
    $obErro = $this->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( !$rsRecordSet->eof() ) {
            foreach ($this->arEstrutura AS $obCampo) {
                $this->setDado( $obCampo->getNomeCampo(), $rsRecordSet->getCampo( $obCampo->getNomeCampo() ) );
            }
        } else {
            $obErro->setDescricao( 'Nenhum registro encontrado!' );
        }
    }

    return $obErro;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaNow.
    * Este método é responsável por retornar o status atual do timestamp no banco.
    * @access Public
    * @param  String  $stNow String contendo o timestamp do banco de dados
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaNow(&$stNow, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = "SELECT now() as now";
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stNow = $rsRecordSet->getCampo("now");
    }

    return $obErro;
}
/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaNow3.
    * Este método é responsável por retornar o status atual do timestamp no banco.
    * @access Public
    * @param  String  $stNow String contendo o timestamp(3) do banco de dados
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaNow3(&$stNow, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = "SELECT now()::timestamp(3) as now";
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stNow = $rsRecordSet->getCampo("now");
    }

    return $obErro;
}
/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método montaRecuperaRelacionamento.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamento().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método montaRecuperaRelacionamentoComEntidades.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamentoComEntidades(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoComEntidades().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Recupera o próximo código da tabela, a partir do atributo stCampoCod setado na Persistente da tabela espefícica.
    * @access Public
    * @param  Integer $inCod Parâmetro de saída contendo o máximo valor do código incrementado em 1
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function proximoCod(&$inCod, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stComplemento = $this->montaComplementoChave();
    if ($stComplemento) {
        $stComplemento = " WHERE ".$stComplemento;
    }
    $stSql = "SELECT COALESCE(MAX(".$this->getCampoCod()."), 0) AS CODIGO FROM ".$this->getTabela().$stComplemento;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $inCod = $rsRecordSet->getCampo("codigo") + 1;
    }

    return $obErro;
}
/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaMaxTimestamp.
    * @access Public
    * @param  Integer $rsRecordSet Objeto RecordSet
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaMaxTimestamp(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaMaxTimestamp();
    $stChave = $this->montaChave();
    if ($stChave) {
        $stSql .= " WHERE ".$stChave;
    }
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function executaRecupera($stMetodo, &$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->$stMetodo().$stFiltro.$stOrder;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function executaRecuperaSql($stSql, &$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql .= $stFiltro.$stOrder;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta o SQL contendo o complemento de chave da tabela.
    * Cada tipo de campo recebe aqui um tratamento diferenciado.
    * @access Public
    * @return String String contendo a parte condicional do SQL
*/
function montaComplementoChave()
{
    $stComplementoChave = str_replace ( " ", "", $this->getComplementoChave() );
    $stRetorno = "";
    if ($stComplementoChave) {
        $arComplementoChave = explode(",", $stComplementoChave);
        foreach ($arComplementoChave as $stCampo) {
            if ($stCampo) {
                $inValor = $this->getDado($stCampo);
                if ($inValor != "") {
                      for ($inCount=0;$inCount<count($this->GetEstrutura());$inCount++) {
                        $obCampo = $this->arEstrutura[$inCount];
                        if ( $obCampo->GetNomeCampo() == $stCampo ) {
                            $obCampo->SetTipoCampo(strtolower($obCampo->GetTipoCampo()));
                            switch ($obCampo->GetTipoCampo()) {
                                case("date"):
                                    $stRetorno .= $stCampo." = TO_DATE('".$inValor."','dd/mm/yyyy') AND ";
                                break;
                                case("timestamp"):
                                case("timestamp_now"):
                                    $stRetorno .= $stCampo." = TO_TIMESTAMP('".$inValor."','yyyy-mm-dd hh24:mi:ss.us') AND ";
                                break;
                                case("timestamp_date"):
                                    $stRetorno .= $stCampo." = TO_TIMESTAMP('".$inValor."','dd/mm/yyyy') AND ";
                                break;
                                case("data_hora"):
                                    $stRetorno .= $stCampo." = TO_DATE(''".$inValor."',,'dd/mm/yyyy') AND ";
                                break;
                                case("char"):
                                case("text"):
                                case("varchar"):
                                case(preg_match('/char.*/', $obCampo->GetTipoCampo()) ? true : false):
                                    $stRetorno .= $stCampo." = '".$inValor."' AND ";
                                break;
                                case("numeric"):
                                    if ( $obCampo->getCampoForeignKey() ) {
                                        $stConteudo = &$this->getDado( $stCampo );
                                    } else {
                                        $stConteudo = $obCampo->GetConteudo();
                                    }
                                    $nrValor = str_replace('.', '', $stConteudo );
                                    $nrValor = str_replace(',', '.', $nrValor );
                                    $stSql.= "\n    '".$nrValor."',";
                                break;
                                case("boolean"):
                                    if ( $obCampo->getCampoForeignKey() ) {
                                        $stConteudo = &$this->getDado( $obCampo->GetNomeCampo() );
                                    } else {
                                        $stConteudo = $obCampo->GetConteudo();
                                    }
                                    if( $stConteudo == "true" || $stConteudo == "t")
                                        $stValor = "true";
                                    elseif( $stConteudo == "false" || $stConteudo == "f" || $stConteudo == false)
                                        $stValor = "false";
                                    $stRetorno .= $stCampo." = '".$stValor."' AND ";
                                break;
                                default:
                                    $stRetorno .= $stCampo." = ".$inValor." AND ";
                                break;
                            }
                        }
                    }
                }

            } else {
                $stRetorno .= $stCampo." IS NULL AND ";
            }
        }
        $stRetorno = substr( $stRetorno, 0, strlen( $stRetorno ) - 4 );
    }

    return $stRetorno;
}
/**
    * Monta o SQL contendo a chave principal da tabela.
    * Cada tipo de campo recebe aqui um tratamento diferenciado.
    * @access Public
    * @return String String contendo a parte condicional do SQL
*/
function montaChave()
{
    $stChave = $this->getCampoCod();
    if ($stChave) {
        $inValor = $this->getDado($stChave);
        if ($inValor != "") {
            foreach ($this->GetEstrutura() as $obCampo) {
                if ( $obCampo->GetNomeCampo() == $stChave ) {
                    switch ( strtolower( $obCampo->GetTipoCampo() ) ) {
                        case("date"):
                            $stChave = $stChave." = TO_DATE(''".$inValor."',,'dd/mm/yyyy') ";
                        break;
                        case("timestamp"):
                        case("timestamp_now"):
                            $stChave = $stChave." = TO_TIMESTAMP('".$inValor."','yyyy-mm-dd hh24:mi:ss.us') ";
                        break;
                        case("timestamp_date"):
                            $stChave = $stChave." = TO_TIMESTAMP('".$inValor."','dd/mm/yyyy') ";
                        break;
                        case("data_hora"):
                            $stChave = $stChave." = TO_DATE(''".$inValor."',,'dd/mm/yyyy') ";
                        break;
                        case("char"):
                        case("text"):
                        case("varchar"):
                            $stChave = $stChave." = '".$inValor."' ";
                        break;
                        case("numeric"):
                            if ( $obCampo->getCampoForeignKey() ) {
                                $stConteudo = &$this->getDado( $stCampo );
                            } else {
                                $stConteudo = $obCampo->GetConteudo();
                            }
                            $nrValor = str_replace('.', '', $stConteudo );
                            $nrValor = str_replace(',', '.', $nrValor );
                            $stSql.= "\n    '".$nrValor."',";
                        break;
                        default:
                            $stChave = $stChave." = ".$inValor." ";
                        break;
                    }
                }
            }
        } else {
            $stChave = $stChave." IS NULL ";
        }
    }
    $stComplemento = $this->montaComplementoChave();
    if ($stComplemento) {
        if( $stChave )
            $stChave = $stChave." AND ".$stComplemento;
        else
            $stChave = $stComplemento;
    }

    return $stChave;
}
/**
    * Monta o SQL a ser chamado no método RecuperaPorChave.
    * Recupera parte do SQL montado pelo método montaRecuperaTodos e outra pelo montaChave.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRecuperaPorChave()
{
    $stSql  = $this->montaRecuperaTodos();
    $stChave = $this->montaChave();
    if ($stChave) {
        $stSql .= " WHERE ".$stChave;
    }

    return $stSql;
}
/**
    * Monta a cláusula SQL inicial (até o FROM table)
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRecuperaTodos()
{
    $stSql = "SELECT ";
    foreach ($this->getEstrutura() as $obCampo) {
        switch ($obCampo->getTipoCampo()) {
            case("date"):
                $stSql.= "\n    TO_CHAR(".$obCampo->getNomeCampo().",'dd/mm/yyyy') AS ".$obCampo->getNomeCampo()." ,";
                break;
            case("timestamp"):
            case("timestamp_now"):
                $stSql.= "\n    TO_CHAR(".$obCampo->getNomeCampo().",'yyyy-mm-dd hh24:mi:ss.us') AS ".$obCampo->getNomeCampo()." ,";
                break;
            case("timestamp_date"):
                $stSql.= "\n    TO_CHAR(".$obCampo->getNomeCampo().",'dd/mm/yyyy') AS ".$obCampo->getNomeCampo()." ,";
                break;
            case("data_hora"):
                $stSql.= "\n    TO_CHAR(".$obCampo->getNomeCampo().",'dd/mm/yyyy hh24:mi:ss') AS ".$obCampo->getNomeCampo()." ,";
                break;
            case("number"):
                $stSql.= "\n    TO_CHAR(".$obCampo->getNomeCampo().",'99D999D999G99') AS ".$obCampo->getNomeCampo()." ,";
                break;
            default:
                $stSql.= "\n    ".$obCampo->getNomeCampo()." ,";
                break;
        }
    }
    $stSql = substr($stSql,0,strlen($stSql) - 1);
    $stSql.= "\nFROM ";
    $stSql.= "\n    ".$this->getTabela();

    return $stSql;
}
/**
    * Monta a cláusula DML contendo os campos e valores necessários para efetuar o INSERT
    * Cada tipo de campo recebe aqui um tratamento diferenciado na inserção.
    * @access Public
    * @return String String contendo o DML INSERT
*/
function MontaInclusao($boTransacao = '', &$arBlob, $obConexao = "")
{
    $arBlob = array( "qtd_blob" => 0 );

    $stSql = "INSERT INTO ".$this->GetTabela()." (";
    foreach ($this->GetEstrutura() as $obCampo) {

        if ( $obCampo->getCampoForeignKey() ) {
            $stConteudo = $this->getDado( $obCampo->GetNomeCampo() );
        } else {
            $stConteudo = $obCampo->GetConteudo();
        }

        if ($obCampo->GetRequerido() or isset($stConteudo) or $stConteudo=='0') {
            $stSql.= "\n    ".$obCampo->GetNomeCampo().",";
        }
    }
    $stSql = substr($stSql,0,strlen($stSql) - 1);
    $stSql.= "\n)VALUES(";
    foreach ($this->GetEstrutura() as $obCampo) {

        if ( $obCampo->getCampoForeignKey() ) {
            $stConteudo = $this->getDado( $obCampo->GetNomeCampo() );
        } else {
            $stConteudo = $obCampo->GetConteudo();
        }

        if ($obCampo->GetRequerido() or isset($stConteudo) or $stConteudo=='0') {
            switch ($obCampo->GetTipoCampo()) {
                case ("oid"):
                    if ($obConexao)
                        $oid = $obConexao->retornaOID($boTransacao);
                    else
                        $oid = "OID debug";

                    $stSql.= "\n    '".$oid."',";

                    $arBlob["blob"][$arBlob["qtd_blob"]] = $stConteudo;
                    $arBlob["blob_oid"][$arBlob["qtd_blob"]] = $oid;
                    $arBlob["qtd_blob"]++;
                    $this->setDado( $obCampo->GetNomeCampo(), $oid );
                    break;

                case("date"):
                    //Verifica se foi passado algum conteúdo na variável
                    //Caso não tenha sido passado é inserido null no campo para que o
                    //registro não fique com a data 01-01-0000 BC
                    if($stConteudo == "" || $stConteudo == null)
                        $stSql.= "\n    null,";
                    else
                        $stSql.= "\n    TO_DATE('".$stConteudo."' ,'dd/mm/yyyy'),";
                break;
                case("data_hora"):
                    $stSql.= "\n    TO_DATE('".$stConteudo."' ,'dd/mm/yyyy hh24:mi:ss'),";
                break;
                case("time"):
                    $stSql.= "\n    TO_TIMESTAMP('".$stConteudo."' ,'hh24:mi'),";
                break;
                case("fulltime"):
                    $stSql.= "\n    TO_TIMESTAMP('".$stConteudo."' ,'hh24:mi:ss'),";
                break;
                case("timestamp"):
                    $stSql.= "\n    TO_TIMESTAMP('".$stConteudo."' ,'yyyy-mm-dd hh24:mi:ss.US'),";
                break;
                case("timestamp_date"):
                    $stSql.= "\n    TO_TIMESTAMP('".$stConteudo."' ,'dd/mm/yyyy'),";
                break;
                case("timestamp_now"):
                    $obErro = $this->recuperaNow3( $stNow , $boTransacao );
                    $this->setDado( $obCampo->GetNomeCampo(), $stNow );
                    $stConteudo = $stNow;
                    $stSql.= "\n    TO_TIMESTAMP('".$stConteudo."' ,'yyyy-mm-dd hh24:mi:ss.us'),";
                break;
                case("char"):
                case("text"):
                case("varchar"):
                    $stSql.= "\n    '".str_replace("'", "''", $stConteudo)."',";
                break;
                case("numeric"):
                    $nrValor = $stConteudo;
                    if (strstr($nrValor,",")) {
                        $nrValor = str_replace('.', '', $nrValor );
                        $nrValor = str_replace(',', '.', $nrValor );
                    }
                    $stSql.= "\n    ".$nrValor.",";
                break;
                case("boolean"):
                    if( $stConteudo == "true" || $stConteudo == "t")
                        $stValor = "true";
                    elseif( $stConteudo == "false" || $stConteudo == "f" || $stConteudo == false)
                        $stValor = "false";
                    $stSql.= "\n    '".$stValor."',";
                break;
                case("hstore"):
                    if (!is_array($stConteudo)) {
                        $stConteudo = array($stConteudo);
                    }

                    $values = array();
                    foreach ($stConteudo as $key => $value) {
                        $values[] = "\"".$key."\" => \"".$value."\"";
                    }
                    $stSql.= "\n    '".implode(",", $values)."',";
                break;
                //
                // Verificar como resolver o problema da transação e do erro no método proximoCod
                //
                /**/
                case("sequence"):
                    if (!$stConteudo) {
                        $obErro = $this->proximoCod( $inProximo , $boTransacao );
                        $this->setDado( $obCampo->GetNomeCampo(), $inProximo );
                        $stConteudo = $inProximo;
                    }
                    $stSql.= "\n    ".$stConteudo." ,";
                break;
                case ("serial"):

                        if (!isset($stConteudo)) {

                            $obErro = $this->proximoSerial($inProximo, $obCampo, false, $boTransacao);
                            $this->setDado($obCampo->GetNomeCampo() , $inProximo);
                            $stConteudo = $inProximo;

                        }
                        $stSql.= "\n    " . $stConteudo . " ,";
                        break;
                /**/
                default:
                    $stSql.= "\n    ".$stConteudo." ,";
                break;
            }
        }
    }
    $stSql = substr($stSql,0,strlen($stSql) - 1)."\n)";

    return $stSql;
}
/**
    * Monta a cláusula DML contendo os campos e valores necessários para efetuar o UPDATE
    * Cada tipo de campo recebe aqui um tratamento diferenciado na alteração.
    * @access Public
    * @return String String contendo o DML UPDATE
*/
function MontaAlteracao(&$arBlob, $obConexao = "", $boTransacao = "")
{
    $arBlob = array( "qtd_blob" => 0 );

    $stSql = "UPDATE ".$this->GetTabela()." SET ";
    foreach ($this->GetEstrutura() as $obCampo) {

        if ( $obCampo->getCampoForeignKey() ) {
            $stConteudo = $this->getDado( $obCampo->GetNomeCampo() );
        } else {
            $stConteudo = $obCampo->GetConteudo();
        }

        if ($obCampo->GetRequerido() or isset($stConteudo) or $stConteudo=='0') {
            switch ($obCampo->GetTipoCampo()) {
                case ("oid"):
                    if ( $obConexao )
                        $oid = $obConexao->retornaOID($boTransacao);
                    else
                        $oid = "OID debug";

                    $stSql.= "    ".$obCampo->GetNomeCampo()."='".$oid."',";

                    $arBlob["blob"][$arBlob["qtd_blob"]] = $stConteudo;
                    $arBlob["blob_oid"][$arBlob["qtd_blob"]] = $oid;
                    $arBlob["qtd_blob"]++;

                    $this->setDado( $obCampo->GetNomeCampo(), $oid );
                    break;

                case("date"):
                    $stSql.= "    ".$obCampo->GetNomeCampo()."=";
                    //Verifica se foi passado algum conteúdo na variável
                    //Caso não tenha sido passado é inserido null no campo para que o
                    //registro não fique com a data 01-01-0000 BC
                    if($stConteudo == "" || $stConteudo == null)
                        $stSql.= "null,";
                    else
                        $stSql.= "TO_DATE('".$stConteudo."' ,'dd/mm/yyyy'),";
                break;
                case("data_hora"):
                    $stSql.= "    ".$obCampo->GetNomeCampo()."=";
                    $stSql.= "TO_DATE('".$stConteudo."' ,'dd/mm/yyyy hh24:mi:ss'),";
                break;
                case("time"):
                    $stSql.= "\n    TO_TIMESTAMP('".$stConteudo."' ,'hh24:mi'),";
                break;
                case("fulltime"):
                    $stSql.= "\n    TO_TIMESTAMP('".$stConteudo."' ,'hh24:mi:ss'),";
                break;
                case("timestamp"):
                case("timestamp_now"):
                    $stSql.= "    ".$obCampo->GetNomeCampo()."=";
                    $stSql.= "TO_TIMESTAMP('".$stConteudo."' ,'yyyy-mm-dd hh24:mi:ss.US'),";
                break;
                case("timestamp_date"):
                    $stSql.= "    ".$obCampo->GetNomeCampo()."=";
                    $stSql.= "TO_TIMESTAMP('".$stConteudo."' ,'dd/mm/yyyy'),";
                break;
                case("char"):
                case("text"):
                case("varchar"):
                    $stSql.= "    ".$obCampo->GetNomeCampo()."=";
                    $stSql.= "\n    '".str_replace("'", "''", $stConteudo)."',";
                break;
                case("numeric"):
                    $stSql.= "    ".$obCampo->GetNomeCampo()."=";
                    $nrValor = $stConteudo;
                    if (strstr($nrValor,",")) {
                        $nrValor = str_replace('.', '', $nrValor );
                        $nrValor = str_replace(',', '.', $nrValor );
                    }
                    $stSql.= "\n    '".$nrValor."',";
                break;
                case("boolean"):
                    $stSql.= "    ".$obCampo->GetNomeCampo()."=";
                    if( $stConteudo == "true" || $stConteudo == "t")
                        $stValor = "true";
                    elseif( $stConteudo == "false" || $stConteudo == "f" || $stConteudo == false)
                         $stValor = "false";
                    $stSql.= "\n    '".$stValor."',";
                break;
                default:
                    $stSql.= "    ".$obCampo->GetNomeCampo()."=";
                    $stSql.= " ".$stConteudo.",";
                break;
            }
        }
    }
    $stSql= substr($stSql,0,strlen($stSql) - 1);

    return $stSql;
}
/**
    * Monta a cláusula DML contendo os campos e valores necessários para efetuar o DELETE
    * @access Public
    * @return String String contendo o DML DELETE
*/
function montaExclusao()
{
    $stSql = " DELETE FROM ".$this->getTabela();

    return $stSql;
}
/**
    * Método pré-definido pela classe Persistente.
    * Caso haja necessidade de utilização em uma classe estendida, basta efetuar uma sobreposição de métodos
    * @access Public
    * @return String String vazia
*/
function montaRecuperaRelacionamento()
{
    return "";
}
/**
    * Monta a cláusula SQL contendo o MAX(timestamp) responsável por pegar o último timestamp da tabela.
    * @access Public
    * @return String String vazia
*/
function montaRecuperaMaxTimestamp()
{
    $stSql = "SELECT MAX(timestamp) as timestamp FROM ".$this->getTabela();

    return $stSql;
}

/**
    * Método pré-definido pela classe Persistente, responsável por efetuar validações da inclusão.
    * Caso haja necessidade de utilização em uma classe estendida, basta efetuar uma sobreposição de métodos
    * @access Public
    * @param  Boolean $boTransacao
    * @return Boolean true

    public function validaInclusao($stFiltro = "" , $boTransacao = "")
    {
        $obErro = new Erro;

        return $obErro;
    }
*/

/**
    * Método pré-definido pela classe Persistente, responsável por efetuar validações da exclusão.
    * Caso haja necessidade de utilização em uma classe estendida, basta efetuar uma sobreposição de métodos
    * @access Public
    * @param  Boolean $boTransacao
    * @return Boolean true
*/
public function validaExclusao($stFiltro = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    
    $stTabelaPrincipal = $this->getTabela();    
    //Busca as tabelas que são referenciadas pela tabela principal
    $stSql = $this->buscaForeingKeys( $stTabelaPrincipal );
    $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao, $obConexao);
    
    if ( !$obErro->ocorreu() ) {
        foreach ($rsRecordSet->getElementos() as $chave => $valor) {
            //Bucar chaves das tabela mae e fk
            list($stTabelaFK,$stTabelaMae) = explode(' REFERENCES ',$valor['descricao_fk']);
            //Retira tudo da string para pegar os campos que vem direto do banco
            $stTabelaFK  = preg_replace("/.*\(/", "", $stTabelaFK);
            $stTabelaMae = preg_replace("/.*\(/", "", $stTabelaMae);
            //Remove aspas dos campo timestamp que vem do banco
            $stTabelaFK  = preg_replace("/\"/", "", $stTabelaFK);
            $stTabelaMae = preg_replace("/\"/", "", $stTabelaMae);
            //Separada campos que vem na string
            $arChaveTabelaFK  = explode(",",$stTabelaFK);
            $arChaveTabelaMae = explode(",",$stTabelaMae);
            //Retira qualquer espaco em branco de todos os campos
            $arChaveTabelaFK  = array_map("trim",$arChaveTabelaFK);
            $arChaveTabelaMae = array_map("trim",$arChaveTabelaMae);
            //-----------------------------------------------------------------------------

            //Monta Select de cada tabela verificando se existe dados vinculados
            $stSelect = "SELECT 1 \n FROM ".$valor['nome_tabela_mae']." , ".$valor['nome_tabela_fk'];
            for($i=0;$i<count($arChaveTabelaMae);$i++){
                if($i == 0 ) {                    
                    $stWhere = "\n WHERE ".$valor['schema_mae'].".".$valor['tabela_mae'].".".$arChaveTabelaMae[$i]." = ".$valor['schema_fk'].".".$valor['tabela_fk'].".".$arChaveTabelaFK[$i];
                } else {
                    $stWhere .= "\n AND ".$valor['schema_mae'].".".$valor['tabela_mae'].".".$arChaveTabelaMae[$i]." = ".$valor['schema_fk'].".".$valor['tabela_fk'].".".$arChaveTabelaFK[$i];                    
                }
            }
            //Monta filtro com os campos de chaves
            $chaveFiltro = $this->montaFiltro();
            $stSelect .= $stWhere."\n AND ".$chaveFiltro;
            
            //Executa o select com a relacao da tabela fk
            $this->setDebug($stSelect);
            $obErro = $obConexao->executaSQL($rsValidaRelacao, $stSelect, $boTransacao, $obConexao);

            //Verifica se existe dados e evita delete no banco que causava erro de fk
            $boValidaExclusao = true;
            if (!$obErro->ocorreu()) {
                if ($rsValidaRelacao->getNumLinhas() > 0 ) {
                    $boValidaExclusao = false;
                }
            }else{
                $boValidaExclusao = false;
            }

            //Seta mensagem padrao para o usuario, deve ser sobrescrita de acordo com cada regra e acao
            if (!$boValidaExclusao) {
                $obErro->setDescricao("Não é possivel excluir os dados porque o está sendo utilizado pelo sistema em outra ação! ");
                return $obErro;
            }
        }
    }
    return $obErro;
}

private function montaFiltro()
{
    $stTabela = $this->getTabela();
    $stChave = $this->getCampoCod();
    if ($stChave) {
        $inValor = $this->getDado($stChave);
        if ($inValor != "") {
            foreach ($this->GetEstrutura() as $obCampo) {
                if ( $obCampo->GetNomeCampo() == $stChave ) {
                    switch ( strtolower( $obCampo->GetTipoCampo() ) ) {
                        case("date"):
                            $stChave = $stTabela.".".$stChave." = TO_DATE(''".$inValor."',,'dd/mm/yyyy') ";
                        break;
                        case("timestamp"):
                        case("timestamp_now"):
                            $stChave = $stTabela.".".$stChave." = TO_TIMESTAMP('".$inValor."','yyyy-mm-dd hh24:mi:ss.us') ";
                        break;
                        case("timestamp_date"):
                            $stChave = $stTabela.".".$stChave." = TO_TIMESTAMP('".$inValor."','dd/mm/yyyy') ";
                        break;
                        case("data_hora"):
                            $stChave = $stTabela.".".$stChave." = TO_DATE(''".$inValor."',,'dd/mm/yyyy') ";
                        break;
                        case("char"):
                        case("text"):
                        case("varchar"):
                            $stChave = $stTabela.".".$stChave." = '".$inValor."' ";
                        break;
                        case("numeric"):
                            if ( $obCampo->getCampoForeignKey() ) {
                                $stConteudo = &$this->getDado( $stCampo );
                            } else {
                                $stConteudo = $obCampo->GetConteudo();
                            }
                            $nrValor = str_replace('.', '', $stConteudo );
                            $nrValor = str_replace(',', '.', $nrValor );
                            $stSql.= "\n    '".$nrValor."',";
                        break;
                        default:
                            $stChave = $stTabela.".".$stChave." = ".$inValor." ";
                        break;
                    }
                }
            }
        } else {
            $stChave = $stTabela.".".$stChave." IS NULL ";
        }
    }
    $stComplemento = $this->montaComplementoFiltro();
    if ($stComplemento) {
        if( $stChave )
            $stChave = $stChave." AND ".$stComplemento;
        else
            $stChave = $stComplemento;
    }

    return $stChave;
}

private function montaComplementoFiltro()
{
    $stTabela = $this->getTabela();
    $stComplementoChave = str_replace ( " ", "", $this->getComplementoChave() );
    $stRetorno = "";
    if ($stComplementoChave) {
        $arComplementoChave = explode(",", $stComplementoChave);
        foreach ($arComplementoChave as $stCampo) {
            if ($stCampo) {
                $inValor = $this->getDado($stCampo);
                if ($inValor != "") {
                      for ($inCount=0;$inCount<count($this->GetEstrutura());$inCount++) {
                        $obCampo = $this->arEstrutura[$inCount];
                        if ( $obCampo->GetNomeCampo() == $stCampo ) {
                            $obCampo->SetTipoCampo(strtolower($obCampo->GetTipoCampo()));
                            switch ($obCampo->GetTipoCampo()) {
                                case("date"):
                                    $stRetorno .= $stTabela.".".$stCampo." = TO_DATE('".$inValor."','dd/mm/yyyy') AND ";
                                break;
                                case("timestamp"):
                                case("timestamp_now"):
                                    $stRetorno .= $stTabela.".".$stCampo." = TO_TIMESTAMP('".$inValor."','yyyy-mm-dd hh24:mi:ss.us') AND ";
                                break;
                                case("timestamp_date"):
                                    $stRetorno .= $stTabela.".".$stCampo." = TO_TIMESTAMP('".$inValor."','dd/mm/yyyy') AND ";
                                break;
                                case("data_hora"):
                                    $stRetorno .= $stTabela.".".$stCampo." = TO_DATE(''".$inValor."',,'dd/mm/yyyy') AND ";
                                break;
                                case("char"):
                                case("text"):
                                case("varchar"):
                                case(preg_match('/char.*/', $obCampo->GetTipoCampo()) ? true : false):
                                    $stRetorno .= $stTabela.".".$stCampo." = '".$inValor."' AND ";
                                break;
                                case("numeric"):
                                    if ( $obCampo->getCampoForeignKey() ) {
                                        $stConteudo = &$this->getDado( $stCampo );
                                    } else {
                                        $stConteudo = $obCampo->GetConteudo();
                                    }
                                    $nrValor = str_replace('.', '', $stConteudo );
                                    $nrValor = str_replace(',', '.', $nrValor );
                                    $stSql.= "\n    '".$nrValor."',";
                                break;
                                case("boolean"):
                                    if ( $obCampo->getCampoForeignKey() ) {
                                        $stConteudo = &$this->getDado( $obCampo->GetNomeCampo() );
                                    } else {
                                        $stConteudo = $obCampo->GetConteudo();
                                    }
                                    if( $stConteudo == "true" || $stConteudo == "t")
                                        $stValor = "true";
                                    elseif( $stConteudo == "false" || $stConteudo == "f" || $stConteudo == false)
                                        $stValor = "false";
                                    $stRetorno .= $stTabela.".".$stCampo." = '".$stValor."' AND ";
                                break;
                                default:
                                    $stRetorno .= $stTabela.".".$stCampo." = ".$inValor." AND ";
                                break;
                            }
                        }
                    }
                }

            } else {
                $stRetorno .= $stTabela.".".$stCampo." IS NULL AND ";
            }
        }
        $stRetorno = substr( $stRetorno, 0, strlen( $stRetorno ) - 4 );
    }

    return $stRetorno;
}

private function buscaForeingKeys($stTabela)
{

    list($stSchema,$stTable) = explode('.',$stTabela);
    
    if (preg_match('/sw_/', $stSchema)){
        $stTable = $stSchema;
        $stSchema = 'public';
    }

    $stSql = "  SELECT  namespace_1.nspname AS schema_mae
                      , class_1.relname     AS tabela_mae
                      , namespace_2.nspname AS schema_fk
                      , class_2.relname     AS tabela_fk
                      , namespace_1.nspname||'.'||class_1.relname as nome_tabela_mae
                      , namespace_2.nspname||'.'||class_2.relname as nome_tabela_fk
                      , REPLACE(regexp_replace(pg_catalog.pg_get_constraintdef(pg_constraint.oid, true),'FOREIGN KEY ',namespace_2.nspname||'.'||class_2.relname),')','') as descricao_fk
                FROM pg_namespace  AS namespace_1
                JOIN pg_class      AS class_1
                  ON namespace_1.oid = class_1.relnamespace
                JOIN pg_constraint
                  ON class_1.oid = pg_constraint.confrelid
                JOIN pg_class      AS class_2
                  ON pg_constraint.conrelid = class_2.oid
                JOIN pg_namespace namespace_2
                  ON class_2.relnamespace = namespace_2.oid
                WHERE namespace_1.nspname = '".$stSchema."'
                AND class_1.relname       = '".$stTable."'
                ORDER BY namespace_1.nspname
                       , class_1.relname
                       , namespace_2.nspname
                       , class_2.relname
        ";
    return $stSql;
}


/**
    * Método pré-definido pela classe Persistente, responsável por efetuar validações da alteração.
    * Caso haja necessidade de utilização em uma classe estendida, basta efetuar uma sobreposição de métodos
    * @access Public
    * @param  Boolean $boTransacao
    * @return Boolean true

    public function validaAlteracao($stFiltro = "" , $boTransacao = "")
    {
        $obErro = new Erro;

        return $obErro;
    }
*/

    public function proximoSerial(&$mxValue, CampoTabela $obCampo, $boRespeitarChave = false, $boTransacao = "")
    {

        /* inicialização */
        $obErro     = new Erro;
        $obConexao  = new Conexao;
        $rsRecordSet= new RecordSet;

        $stChave    =  '';

        /* deve respeitar a PK da tabela?*/
        if (true === $boRespeitarChave) {

            $stChave = " WHERE " . $this->montaChave();

        }

        /* obtem maior serial */
        $stSql = "SELECT MAX(" . $obCampo->GetNomeCampo() . ") as proximo_serial FROM " . $this->getTabela() . $stChave;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        if (!$obErro->ocorreu()) {
            $inProximoSerial = (int) $rsRecordSet->getCampo("proximo_serial");
            /* então incrementa maior serial em 1*/
            $mxValue = $inProximoSerial + 1;

        }

        return $obErro;

    }

}
