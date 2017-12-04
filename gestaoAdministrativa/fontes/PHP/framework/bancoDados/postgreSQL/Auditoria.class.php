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
 * Classe responsável pela inserção de auditoria no banco de dados
 * @author Desenvolvedor: Marcelo Boezzio Paulino
 *
 * @package bancoDados
 * @subpackage postgreSQL
 *
 * $Id: Auditoria.class.php 65296 2016-05-10 17:56:10Z michel $
 *
 * Casos de uso: uc-01.01.00
*/
class Auditoria
{
    /**
     * @var Integer
     */
    private $inNumCGM;

    /**
     * @var Integer
     */
    private $inCodAcao;

    /**
     * @var array
     */
    private $stObjeto;

    /**
     * @var Boolean
     */
    private $boTransacao;

    /**
     * @var DateTime
     */
    private $dtTimestamp;

    /**
     * @var Array
     */
    private $arDetalhes;

    /**
     * @var resource
     */
    private static $obConnection;

    /**
     * @param Integer $valor
     */
    public function setNumCGM($valor)
    {
        $this->inNumCGM = $valor;
    }

    /**
     * @param Integer $valor
     */
    public function setCodAcao($valor)
    {
        $this->inCodAcao = $valor;
    }

    /**
     * @param array $valor
     */
    public function setObjeto($valor)
    {
        $this->stObjeto = $valor;
    }

    /**
     * @param Boolean $valor
     */
    public function setTransacao($valor)
    {
        $this->boTransacao = $valor;
    }

    /**
     * @param Datetime $valor
     */
    public function setTimestamp($valor)
    {
        $this->dtTimestamp = $valor;
    }

    /**
     * @param resource $valor
     */
    public static function setConnection($valor)
    {
        self::$obConnection = $valor;
    }

    /**
     * @return Integer
     */
    public function getNumCGM()
    {
        return $this->inNumCGM;
    }

    /**
     * @return Integer
     */
    public function getCodAcao()
    {
        return $this->inCodAcao;
    }

    /**
     * @return String
     */
    public function getObjeto()
    {
        return $this->stObjeto;
    }

    /**
     * @return Boolean
     */
    public function getTransacao()
    {
        return $this->boTransacao;
    }

    /**
     * @return String
     */
    public function getTimestamp()
    {
        return $this->dtTimestamp;
    }

    /**
     * @return Array
     */
    public function getDetalhes()
    {
        return $this->arDetalhes;
    }

    /**
     * @return resource
     */
    public static function getConnection()
    {
        return self::$obConnection;
    }

    /**
     * Método Construtor
     */
    public function __construct($boTransacao = null)
    {
        $this->setNumCGM(Sessao::read('numCgm')); //Usuário logado.
        $this->setCodAcao(Sessao::read('acao')); //Ação que está sendo executada.
        $stDate = date("Y/m/d H:i:s"). substr((string) microtime(), 1, 6);
        $this->setTimestamp($stDate);

        $this->arDetalhes = array();

        if (!is_null($boTransacao)) {
            $this->setTransacao($boTransacao);
        }
    }

    /**
     * Inclui um registro na Auditoria.
     *
     * @param  Integer Tipo de Operação (1 => Inclusão; 2 => Alteração; 3 => Exclusão)
     * @param  Object  Objeto Mapeamento
     * @param  Boolean Transação
     * @return Object Erro
     */
    public function incluiAuditoria($obMapeamento, $boTransacao="")
    {
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAuditoria.class.php");

        $obTAuditoria = new TAuditoria();
        $obErro = new Erro();

        if (!empty($obMapeamento) && is_subclass_of($obMapeamento, "Persistente")) {
            $obTAuditoria->setDado("numcgm"   , $this->getNumCGM());
            $obTAuditoria->setDado("cod_acao" , $this->getCodAcao());
            $obTAuditoria->setDado("transacao", $this->getTransacao());
            $stDate = date("Y/m/d H:i:s"). substr((string) microtime(), 1, 6);
            $this->setTimestamp($stDate);
            $obTAuditoria->setDado("timestamp", $this->getTimestamp());

            $values = array();

            //Chaves
            $chave = $obMapeamento->getCampoCod();
            if (!empty($chave)) {
                foreach (explode(",", $chave) as $key) {
                    $values[$key] = $obMapeamento->getDado(trim($key));
                }
            }

            //Complemento de Chaves
            $complementoChave = $obMapeamento->getComplementoChave();
            if (!empty($complementoChave)) {
                foreach (explode(",", $complementoChave) as $key) {
                    $values[$key] = $obMapeamento->getDado(trim($key));
                }
            }

            $this->setObjeto($values);
            $obTAuditoria->setDado("objeto", $this->getObjeto());

            $obErro = $obTAuditoria->inclusao($boTransacao);

            // Grava o detalhes do que foi alterado.
            if (!$obErro->ocorreu()) {
                $arDetalhes = Sessao::read('arAuditoriaDetalhes');
                if (is_null($arDetalhes)) {
                    $arDetalhes = array();
                }

                // foreach ($arDetalhes as $obDetalhe) {
                    // $obDetalhe->setDado('timestamp', $obDetalhe)
                // }
            }
        }

        return $obErro;
    }

    public function adicionarDetalhe($obMapeamento)
    {
        if (!empty($obMapeamento) && is_subclass_of($obMapeamento, "Persistente")) {
            $this->arDetalhes[] = serialize($obMapeamento);
        }
    }

    public function salvar($obMapeamento, $boTransacao)
    {
        include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoAuditoria.class.php";
        include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoAuditoriaDetalhe.class.php";

        $obTAuditoria = new TAuditoria();
        $obErro = new Erro();
        global $request;

        if (!empty($obMapeamento) && is_subclass_of($obMapeamento, "Persistente")) {
            $obTAuditoria->setDado("numcgm"   , $this->getNumCGM());
            $obTAuditoria->setDado("cod_acao" , $this->getCodAcao());
            $obTAuditoria->setDado("transacao", $this->getTransacao());
            $stDate = date("Y/m/d H:i:s"). substr((string) microtime(), 1, 6);
            $this->setTimestamp($stDate);
            $obTAuditoria->setDado("timestamp", $this->getTimestamp());

            $stTimestampAuditoria = $obTAuditoria->getDado("timestamp");

            $arValores = array();
            $arChavesSessao = array();

            //Chaves
            $stChave = $obMapeamento->getCampoCod();

            if (!empty($stChave)) {
                foreach (explode(",", $stChave) as $key) {
                    $arValores[$key] = $obMapeamento->getDado(trim($key));
                }
            }

            //Complemento de Chaves
            $stComplementoChave = $obMapeamento->getComplementoChave();

            if (!empty($stComplementoChave)) {
                foreach (explode(",", $stComplementoChave) as $key) {
                    $arValores[$key] = $obMapeamento->getDado(trim($key));
                }
            }

            $this->setObjeto($arValores);
            $obTAuditoria->setDado("objeto", $this->getObjeto());

            $obErro = $obTAuditoria->inclusao($boTransacao);

            // Grava o detalhes da Auditoria
            if (!$obErro->ocorreu()) {
                //Conexão
                $obConexao = new Conexao();

                if (is_null(Auditoria::getConnection())) {
                    $obConexaoAuditoria = $obConexao->abreConexao(true);
                    Auditoria::setConnection($obConexaoAuditoria);
                }

                $obTAuditoriaDetalhe = new TAuditoriaDetalhe($boTransacao);
                $obTAuditoriaDetalhe->setDado("numcgm"     , $this->getNumCGM());
                $obTAuditoriaDetalhe->setDado("cod_acao"   , $this->getCodAcao());
                $obTAuditoriaDetalhe->setDado("timestamp"  , $stTimestampAuditoria);

                foreach ($this->getDetalhes() as $obDetalheSerialized) {
                    $obTAuditoriaDetalhe->proximoCod( $inCodDetalhe , $boTransacao );
                    $obTAuditoriaDetalhe->setDado('cod_detalhe', $inCodDetalhe);

                    $obDetalhe = unserialize($obDetalheSerialized);

                    $arValores = array();

                    // Caso não seja uma inclusão, ou uma exclusão, grava além dos campos necessários
                    //para que a informação seja única (isto é, as PKs da tabela), as informações alteradas, e etc;

                    # Não está sendo utilizada na funcionalidade, ocorre que a classe Request (nova) nem sempre existe, errando um fatal error por não existir o método Get.
                    #$stAcao = $request->get("stAcao");

                    $arValores['tabela'] = $obDetalhe->getTabela();
                    $arValores['acao'] = $obDetalhe->getDebug();

                    if ($obDetalhe->getDebug() == "alteracao") {
                        $rsRecordSet = new RecordSet();

                        //SQL comentado para talvez ser usado após testes
                        //$stSql = $obDetalhe->montaRecuperaPorChave(); // SQL para achar o registro original
                        //$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, true, Auditoria::getConnection());

                        //Busca da sessão os dados antes de serem alterados pelo método na Persistente para se fazer as comparações
                        $rsRecordSet = Sessao::read("rsAltera_".$obDetalhe->getTabela());

                        if (!$rsRecordSet OR  $rsRecordSet == NULL) {
                            $obErro->setDescricao( "Erro Auditado!" ); //Verificar mensagem de erro
                            Sessao::remove("rsAltera_".$obDetalhe->getTabela());

                            return $obErro;
                        }

                        // Foi necessário preparar o array na persistente para trazer várias alterações de uma mesma tabela
                        //, está buscando os dados na ordem em que foi salvo

                        $arChavesSessao[] = "rsAltera_".$obDetalhe->getTabela();
                        $arRecordSet = array_shift($rsRecordSet->arElementos);

                        Sessao::write('rsAltera_'.$obDetalhe->getTabela(), $rsRecordSet);

                        if (is_array($arRecordSet) && count($arRecordSet) > 0) {
                            foreach ($arRecordSet as $stCampo => $value) {
                                if ($stCampo == 'timestamp' AND $obDetalhe->getDado($stCampo) == '') {
                                    if ($value != date('Y-m-d h:i:s.u')) {
                                        $arValores[$stCampo."_antigo"] = $value;
                                        $arValores[$stCampo."_novo"] = str_replace('/','-',$this->getTimestamp());
                                    }
                                } elseif ($value != $obDetalhe->getDado($stCampo)) {
                                    $valueAntigo = $value;
                                    $valueNovo = $obDetalhe->getDado($stCampo);

                                    //char(34) => aspas(") e chr(92) => contra barra(\).
                                    //Necessário substituir aspas(") por contra barra + aspas(\") para salvar no campo valores, em administracao.auditoria_detalhe.
                                    //Campo valores é do tipo hstore.
                                    if (substr_count($valueAntigo, chr(92).chr(34)) <= 0)
                                        $valueAntigo = str_replace(chr(34), chr(92).chr(34), $valueAntigo);
                                    if (substr_count($valueNovo, chr(92).chr(34)) <= 0)
                                        $valueNovo = str_replace(chr(34), chr(92).chr(34), $valueNovo);

                                    $arValores[$stCampo."_antigo"] = $valueAntigo;
                                    $arValores[$stCampo."_novo"] = $valueNovo;
                                }
                            }
                        }
                    } else {
                        $arEstrutura = $obDetalhe->getEstrutura();

                        foreach ($arEstrutura as $count => $key) {
                            if ($key->getConteudo() != '' OR $key->getConteudo() != NULL) {
                                $valueConteudo = $key->getConteudo();

                                //Fora colocado essa verificação porque na classe $request é feito um addslashes e está adicionando \ 
                                //assim ao adicionar o \, o str_replace está adicionando novamente a \, então esse str_count verifica
                                //se já existem \" para não replicar mais
                                //char(34) => aspas(") e chr(92) => contra barra(\).
                                if (substr_count($valueConteudo, chr(92).chr(34)) <= 0)
                                    $valueConteudo = str_replace(chr(34), chr(92).chr(34), $valueConteudo);

                                $arValores[$key->getNomeCampo()] = $valueConteudo;
                            }
                        }
                    }

                    if (count($arValores) > 2) {
                        $obTAuditoriaDetalhe->setDado("valores", str_replace("'", "''", $arValores));
                        $obErro = $obTAuditoriaDetalhe->inclusao($boTransacao);
                        if ($obErro->ocorreu()) {
                            return $obErro;
                        }
                    }
                }
            }

            if (count($arChavesSessao) > 0) {
                foreach ($arChavesSessao AS $stChaveSessao) {
                    Sessao::remove($stChaveSessao);
                }
            }
        }

        return $obErro;
    }
}
