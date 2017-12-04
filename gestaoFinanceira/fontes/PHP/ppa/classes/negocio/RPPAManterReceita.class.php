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
 * Classe de negócio Manter Receita
 * Data de Criação: 23/09/2008
 *
 * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
 *
 *
 * Caso de Uso: uc-02.09.05
 *
 * $Id $
 */
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPA.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAReceita.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAReceitaDados.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAReceitaRecurso.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAReceitaRecursoValor.class.php';
require_once CAM_GF_PPA_MAPEAMENTO . 'TPPAReceitaInativaNorma.class.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAPPANorma.class.php';
include_once CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoRecurso.class.php';
include_once CAM_GF_PPA_NEGOCIO    . 'RPPAManterAcao.class.php';

class RPPAManterReceita
{

    /**
    * @var Transacao
    */
    private $obTransacao;

    /**
    * Armazena os erros da camada
    *
    * @var string
    */
    private $erro;

    /**
    * Objeto mapeamento TPPAReceita
    *
    * @var object
    */
    private $obTPPAReceita;

    /**
    * Objeto mapeamento TPPAReceitaDados
    *
    * @var object
    */
    private $obTPPAReceitaDados;

    /**
    * Objeto mapeamento TPPAReceitaRecurso
    *
    * @var object
    */
    private $obTPPAReceitaRecurso;

    /**
    * Objeto mapeamento TPPAReceitaRecursoValor
    *
    * @var object
    */
    private $obTPPAReceitaRecursoValor;

    /**
    * Objeto mapeamento TPPAReceitaInativaNorma
    *
    * @var object
    */
    private $obTPPAReceitaInativaNorma;

    /**
    * Objeto mapeamento TPPAPPANorma
    *
    * @var object
    */
    private $obTPPANorma;

    /**
     * Objeto mapeamento TOrcamentoRecurso
     *
     * @var object
     */
     private $obTOrcamentoRecurso;

    /**
     * Atributo de controle se o registro é
     * inclusão ou alteração
     *
     * @var bool
     */
    private $boNovoRegistro;

    /**
    * Construtor da classe:
    * instancia DAOs utilizados pela classe
    *
    */
    public function __construct()
    {
        $this->obTransacao               = new Transacao;
        $this->obTPPAReceita             = new TPPAReceita;
        $this->obTPPAReceitaDados        = new TPPAReceitaDados;
        $this->obTPPAReceitaRecurso      = new TPPAReceitaRecurso;
        $this->obTPPAReceitaRecursoValor = new TPPAReceitaRecursoValor;
        $this->obTPPAReceitaInativaNorma = new TPPAReceitaInativaNorma;
        $this->obTPPANorma               = new TPPAPPANorma;
        $this->obTOrcamentoRecurso       = new TOrcamentoRecurso;
    }

    /**
     * Método utilizado pela classe controladora para
     * informar se a ação de de inclusão ou alteração.
     *
     * @param bool $boValor
     */
    public function setNovoRegistro($boValor)
    {
        $this->boNovoRegistro = $boValor;
    }

    /**
    * Retorna os erros da camada Negócio
    *
    * @return string
    */
    public function getErro()
    {
        return $this->erro;
    }

    /**
     * Valida os dados para o objeto TPPAReceita,
     * postados pelo FM ou LS, e padroniza o nome dos índices.
     *
     * @param array $arParametros
     */
    private function validarDadosReceita(array &$arParametros)
    {
        if (!empty($arParametros['cod_receita'])) {
            // Parâmetros postados pelo LSManterReceita
            $arParametros['inCodNorma']        = (int) $arParametros['cod_norma'];
            $arParametros['inCodReceita']      = (int) $arParametros['cod_receita'];
            $arParametros['inCodConta']        = (int) $arParametros['cod_conta'];
            $arParametros['stExercicio']       = $arParametros['exercicio'];
            $arParametros['inCodPPA']          = (int) $arParametros['cod_ppa'];
            $arParametros['inCodEntidade']     = (int) $arParametros['cod_entidade'];
        } else {
            $arParametros['inCodNorma']        = (int) $arParametros['inCodNorma'];
            $arParametros['inCodReceita']      = (int) $arParametros['inCodReceita'];
            $arParametros['inCodConta']        = (int) $arParametros['inCodConta'];
            $arParametros['stExercicio']       = trim($arParametros['stExercicio']);
            $arParametros['inCodPPA']          = (int) $arParametros['inCodPPA'];
            $arParametros['inCodEntidade']     = (int) $arParametros['inCodEntidade'];
        }
    }

    /**
     * Invoca classe de mapeamento com chamada e critério em uma só etapa.
     *
     * @param  string    $stMapeamento o nome da classe de mapeamento
     * @param  string    $stMetodo     o método invocado para a classe de mapeamento
     * @param  string    $stFiltro     o critério que delimita a busca
     * @return RecordSet
     * @author Pedro Medeiros
     */
    public function pesquisar($stMapeamento, $stMetodo, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $obMapeamento = new $stMapeamento();
        $obMapeamento->$stMetodo($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

        return $rsRecordSet;
    }

    /**
    * Verifica se uma Receita já está cadastrada para o PPA.
    *
    * @param array $arParametros
    * @return bool true se receita encontrada
    */
    public function verificarCadastroReceitaPPA(array $arParametros)
    {
        $this->buscarReceitaPPA($rsReceita, $arParametros);
        $numRegistros = count($rsReceita->arElementos);
        if ($numRegistros > 0) {
            for ($i = 0; $i < $numRegistros; $i++) {
                if ($rsReceita->arElementos[$i]['ativo'] == 't') {
                    return true;
                }
            }
        } else {
            return false;
        }

    }

    /**
     * Verifica se a Norma já está cadastrada para a Receita
     * que esta sendo alterada.
     *
     * @param  array $arParametros
     * @param  bool  $boTransacao
     * @return int
     */
    public function verificarCadastroReceitaNorma($arParametros, $boTransacao = '')
    {
        $inCodReceita = (int) $arParametros['inCodReceita'];
        $inCodPPA     = (int) $arParametros['inCodPPA'];
        $stExercicio  = $arParametros['stExercicio'];
        $inCodConta   = (int) $arParametros['inCodConta'];
        $inCodNorma   = (int) $arParametros['inCodNorma'];
        $obRSReceitaDadosNorma = null;
        $stFiltro  = " WHERE PPARD.cod_receita = $inCodReceita      \n";
        $stFiltro .= "   AND PPARD.cod_ppa     = $inCodPPA          \n";
        $stFiltro .= "   AND PPARD.exercicio   = '$stExercicio'     \n";
        $stFiltro .= "   AND PPARD.cod_conta   = $inCodConta        \n";
        $stFiltro .= "   AND PPARD.cod_norma   = $inCodNorma        \n";
        $this->obTPPAReceitaDados->recuperaReceitaDadosNorma($obRSReceitaDadosNorma, $stFiltro, $boTransacao);
        if (!$obRSReceitaDadosNorma instanceof RecordSet) {
            SistemaLegado::exibeAviso(urlencode('Erro ao verificar o cadastro da Norma!'), 'n_' . __FUNCTION____, 'erro');

            return false;
        }

        return count($obRSReceitaDadosNorma->arElementos);
    }

    /**
     * Invoca classe de mapeamento com chamada e critério em uma só etapa.
     *
     * @param string $stMapeamento o nome da classe de mapeamento
     * @param string $stMetodo     o método invocado para a classe de mapeamento
     * @param string $stCriterio   o critério que delimita a busca
     * @param string $stOrdem      Cláusula ORDER BY
     *
     * @return RecordSet
     */
    private function recuperaMapeamento($stMapeamento, $stMetodo, $stCriterio = '', $stOrdem = '', $boTransacao = '')
    {
        $obMapeamento = new $stMapeamento();
        $obMapeamento->$stMetodo($rsRecordSet, $stCriterio, $stOrdem, $boTransacao);

        return $rsRecordSet;
    }

    /**
    * Recupera os valores do Recurso de uma Receita
    *
    * @param array $arChaves
    * @return object RecordSet
    */
    public function recuperaReceitaRecursoValor(array $arChaves)
    {
        $stMapeamento  = 'TPPAReceitaRecursoValor';
        $stMetodo      = 'recuperaReceitaRecursoValor';
        $stCriterio    = ' WHERE PRRV.cod_receita       = '  . $arChaves['cod_receita'];
        $stCriterio   .= '   AND PRRV.cod_ppa           = '  . $arChaves['cod_ppa'];
        $stCriterio   .= "   AND PRRV.exercicio         = '" . $arChaves['exercicio'] . "'";
        $stCriterio   .= '   AND PRRV.cod_conta         = '  . $arChaves['cod_conta'];
        $stCriterio   .= '   AND PRRV.cod_entidade      = '  . $arChaves['cod_entidade'];
        $stCriterio   .= '   AND PRRV.cod_receita_dados = '  . $arChaves['cod_receita_dados'];
        $stCriterio   .= "   AND PRRV.exercicio_recurso = '" . $arChaves['exercicio_recurso'] . "'";
        $stCriterio   .= '   AND PRRV.cod_recurso       = '  . $arChaves['cod_recurso'];
        $stOrdem     = ' ORDER BY PRRV.cod_recurso';
        $stSeparador = '';

        return $this->recuperaMapeamento($stMapeamento, $stMetodo, $stCriterio, $stOrdem);
    }

    /**
     * Recupera lista de Receitas e seus relacionamentos
     *
     * @param  int    $inCodPPA
     * @param  int    $inCodReceita
     * @param  int    $inCodRecurso
     * @return object RecordSet
     */
    public function recuperaListaReceitas($inCodPPA, $inCodConta = 0)
    {
        $stMapeamento = 'TPPAReceita';
        $stMetodo     = 'recuperaListaReceitas';
        $stCriterio   = ' WHERE pr.cod_ppa = '. $inCodPPA;
        if ($inCodConta > 0) {
            $stCriterio  .= ' AND pr.cod_conta = ' . $inCodConta;
        }
        $stCriterio .= " AND pr.ativo = 't'";
        $stGroupBy  = " GROUP BY pr.cod_receita,                            \n";
        $stGroupBy .= "          pr.cod_ppa,                                \n";
        $stGroupBy .= "          pr.exercicio,                              \n";
        $stGroupBy .= "          pr.cod_conta,                              \n";
        $stGroupBy .= "          OCR.cod_estrutural,                        \n";
        $stGroupBy .= "          pr.cod_entidade,                           \n";
        $stGroupBy .= "          pr.valor_total,                            \n";
        $stGroupBy .= "          ppa.ano_inicio,                            \n";
        $stGroupBy .= "          ppa.ano_final,                             \n";
        $stGroupBy .= "          ppa.destinacao_recurso,                    \n";
        $stGroupBy .= "          OCR.descricao,                             \n";
        $stGroupBy .= "          PN.cod_norma,                              \n";
        $stGroupBy .= "          CGM.nom_cgm                                \n";
        $stCriterio .= $stGroupBy;
        $stOrdem     = ' ORDER BY pr.cod_conta';

        return $this->recuperaMapeamento($stMapeamento, $stMetodo, $stCriterio, $stOrdem);
    }

    /**
     * Recupera os valores do Recurso de uma Receita
     *
     * @param  array  $arParametros
     * @return object RecordSet
     *
     */
    public function recuperaListaReceitaRecursos(array $arParametros)
    {
        $stMapeamento = 'TPPAReceitaRecurso';
        $stMetodo     = 'recuperaReceitaRecurso';
        $stCriterio  = ' WHERE prr.cod_receita = ';
        $stCriterio .= $arParametros['inCodReceita'] . " \n";
        $stCriterio .= ' AND prr.cod_ppa = ';
        $stCriterio .= $arParametros['inCodPPA'] . " \n";
        $stCriterio .= " AND prr.exercicio = '";
        $stCriterio .= $arParametros['stExercicio'] . "' \n";
        $stCriterio .= ' AND prr.cod_conta = ';
        $stCriterio .= $arParametros['inCodConta'] . " \n";
        $stCriterio .= ' AND prr.cod_entidade = ';
        $stCriterio .= $arParametros['inCodEntidade'] . " \n";
        $stCriterio .= ' AND prr.cod_receita_dados = ';
        $stCriterio .= $arParametros['inCodReceitaDados'] . " \n";
        $stOrdem     = ' ORDER BY prr.cod_recurso';

        return $this->recuperaMapeamento($stMapeamento, $stMetodo, $stCriterio, $stOrdem);
    }

    /**
     * Grava os Recursos da Receita e seus valores (versão genérica usada por salvaReceita)
     * @author Pedro Vaz de Mello de Medeiros <pedro.medeiros@cnm.org.br>
     * @param  array   $arParametros para inclusão de Recurso
     * @param  boolean $boTransacao  a transação atual, se houver
     * @return Erro
     */
    private function salvaRecurso($stAcao, $arParametros, $arCamposRecurso, $boTransacao = '')
    {
        $obMRecurso      = new TPPAReceitaRecurso();
        $obMRecursoValor = new TPPAReceitaRecursoValor();
        $obErro          = new Erro();

        # Validar dados antes de incluir.
        $inCodReceita      = (int) $arParametros['inCodReceita'];
        $stExercicio       = $arParametros['stExercicio'];
        $inCodConta        = (int) $arParametros['inCodConta'];
        $inCodEntidade     = (int) $arParametros['inCodEntidade'];
        $inCodPPA          = (int) $arParametros['inCodPPA'];
        $inCodReceitaDados = (int) $arParametros['inCodReceitaDados'];

        if (!is_array($arCamposRecurso) || !count($arCamposRecurso)) {
            $obErro->setDescricao('nenhum código de Recurso definido');

            return $obErro;
        }

        # Inclui dados na tabela receita_recurso.
        $obMRecurso->setDado('cod_receita', $inCodReceita);
        $obMRecurso->setDado('cod_ppa', $inCodPPA);
        $obMRecurso->setDado('exercicio', $stExercicio);
        $obMRecurso->setDado('cod_conta', $arCamposRecurso['inCodConta']);
        $obMRecurso->setDado('cod_entidade', $inCodEntidade);
        $obMRecurso->setDado('cod_receita_dados', $inCodReceitaDados);
        $obMRecurso->setDado('exercicio_recurso', $arCamposRecurso['stExercicio']);
        $obMRecurso->setDado('cod_recurso', $arCamposRecurso['inCodRecurso']);
        $obErro = $obMRecurso->$stAcao($boTransacao);

        if ($obErro->ocorreu()) {
            return $obErro;
        }

        # Grava os valores de cada ano do Recurso
        if (!isset($arCamposRecurso['flAno1']) || $arCamposRecurso['flAno1'] == 0) {
            # Definido Recurso para o total apenas.
            $obMRecursoValor->setDado('cod_receita', $inCodReceita);
            $obMRecursoValor->setDado('cod_ppa', $inCodPPA);
            $obMRecursoValor->setDado('exercicio', $stExercicio);
            $obMRecursoValor->setDado('cod_conta', $inCodConta);
            $obMRecursoValor->setDado('cod_entidade', $inCodEntidade);
            $obMRecursoValor->setDado('cod_receita_dados', $inCodReceitaDados);
            $obMRecursoValor->setDado('exercicio_recurso', $arCamposRecurso['stExercicio']);
            $obMRecursoValor->setDado('cod_recurso', $arCamposRecurso['inCodRecurso']);
            $obMRecursoValor->setDado('ano', '0');
            $obMRecursoValor->setDado('valor', $arCamposRecurso['flTotal']);
            $obErro = $obMRecursoValor->$stAcao($boTransacao);
        } else {
            # Definir valor para Recurso de todos os anos.
            for ($i = 1; $i < 5; ++$i) {
                $obMRecursoValor->setDado('cod_receita', $inCodReceita);
                $obMRecursoValor->setDado('cod_ppa', $inCodPPA);
                $obMRecursoValor->setDado('exercicio', $stExercicio);
                $obMRecursoValor->setDado('cod_conta', $inCodConta);
                $obMRecursoValor->setDado('cod_entidade', $inCodEntidade);
                $obMRecursoValor->setDado('cod_receita_dados', $inCodReceitaDados);
                $obMRecursoValor->setDado('exercicio_recurso', $arCamposRecurso['stExercicio']);
                $obMRecursoValor->setDado('cod_recurso', $arCamposRecurso['inCodRecurso']);
                $obMRecursoValor->setDado('ano', $i);
                $obMRecursoValor->setDado('valor', $arCamposRecurso['flAno' . $i]);
                $obErro = $obMRecursoValor->$stAcao($boTransacao);

                if ($obErro->ocorreu()) {
                    return $obErro;
                }
            }
        }

        return $obErro;
    }

    /**
     * Exclui Recurso da Receita (versão genérica usada por excluiReceita)
     *
     * @author Pedro Vaz de Mello de Medeiros <pedro.medeiros@cnm.org.br>
     *
     * @param  array   $arParametros    elementos para exclusão de Recurso
     * @param  array   $arCamposRecurso elementos para exclusão de Recurso
     * @param  boolean $boTransacao     a transação atual, se houver
     * @return Erro
     */
    private function excluiRecurso($arParametros, $arCamposRecurso, $boTransacao = '')
    {
        $obMRecurso      = new TPPAReceitaRecurso();
        $obMRecursoValor = new TPPAReceitaRecursoValor();

        # Exclui os valores de cada Recurso
        if (!isset($arCamposRecurso['flAno1']) || $arCamposRecurso['flAno1'] == 0) {
            # Definido Recurso para o total apenas.
            $obMRecursoValor->setDado('exercicio', $arCamposRecurso['stExercicio']);
            $obMRecursoValor->setDado('cod_recurso', $arCamposRecurso['inCodRecurso']);
            $obMRecursoValor->setDado('ano', '0');
            $obMRecursoValor->setDado('exercicio_receita', $arCamposRecurso['stExercicio']);
            $obMRecursoValor->setDado('cod_receita_dados', $arCamposRecurso['inCodReceitaDados']);
            $obMRecursoValor->setDado('cod_receita', $arCamposRecurso['inCodReceita']);
            $obMRecursoValor->setDado('cod_ppa', $arParametros['inCodPPA']);
            $obMRecursoValor->setDado('cod_conta', $arCamposRecurso['inCodConta']);
            $obErro = $obMRecursoValor->exclusao($boTransacao);
        } else {
            # Definido Recurso para todos os anos.
            for ($i = 1; $i < 5; ++$i) {
                $obMRecursoValor->setDado('exercicio', $arCamposRecurso['stExercicio']);
                $obMRecursoValor->setDado('cod_recurso', $inCodRecurso);
                $obMRecursoValor->setDado('ano', $i);
                $obMRecursoValor->setDado('exercicio_receita', $stExercicio);
                $obMRecursoValor->setDado('cod_receita_dados', $inCodReceitaDados);
                $obMRecursoValor->setDado('cod_receita', $inCodReceita);
                $obMRecursoValor->setDado('cod_ppa', $inCodPPA);
                $obMRecursoValor->setDado('cod_conta', $inCodConta);
                $obErro = $obMRecursoValor->exclusao($boTransacao);

                if ($obErro->ocorreu()) {
                    return $obErro;
                }
            }
        }

        # Exclui valores da tabela ppa.ppa_receita_recurso.
        $obMRecurso->setDado('cod_recurso', $inCodRecurso);
        $obMRecurso->setDado('exercicio', $arCamposRecurso['stExercicio']);
        $obMRecurso->setDado('cod_conta', $arCamposRecurso['inCodConta']);
        $obMRecurso->setDado('cod_ppa', $inCodPPA);
        $obMRecurso->setDado('cod_receita', $inCodReceita);
        $obMRecurso->setDado('cod_receita_dados', $inCodReceitaDados);
        $obMRecurso->setDado('exercicio_receita', $stExercicio);

        return $obMRecurso->exclusao($boTransacao);
    }

    /**
     * Procura um Recurso pelo código num array de Recursos.
     * @param  array      $arRecursos   lista dos Recursos
     * @param  integer    $inCodRecurso código do recurso a procurar
     * @return array|null campos do recurso encontrado ou null caso contrário
     */
    private function encontraRecurso(array $arRecursos, $inCodRecurso)
    {
        foreach ($arRecursos as $arCampos) {
            if ($arCampos['inCodRecurso'] == $inCodRecurso) {
                return $arCampos;
            }
        }

        return null;
    }

    /**
     * Inclui/Altera Recursos de uma Receita.
     *
     * @author Pedro de Medeiros <pedro.medeiros@cnm.org.br>
     *
     * @param  string  $stAcao       inclusao|alteracao
     * @param  array   $arParametros parâmetros necessários para inclusão ou alteração
     * @param  boolean $boTransacao  se dentro de uma transação
     * @return Erro
     */
    private function salvaRecursos($stAcao, $arParametros, $boTransacao = '')
    {
        $obErro = new Erro();
        $arRecursosNovos   = $arParametros['arRecursos'];
        $arRecursosAntigos = array();

        if ($stAcao == 'alteracao') {
            # Procura todos os recurso antigos para comparação.
            $stFiltro  = ' WHERE ';
            $stFiltro .= 'recurso.cod_receita = ' . $arParametros['inCodReceita'] . ' AND ';
            $stFiltro .= 'recurso.cod_ppa = ' . $arParametros['inCodPPA'] . ' AND ';
            $stFiltro .= 'recurso.exercicio = ' . $arParametros['stExercicio'] . ' AND ';
            $stFiltro .= 'recurso.cod_conta = ' . $arParametros['inCodConta'] . ' AND ';
            $stFiltro .= 'recurso.cod_entidade = ' . $arParametros['inCodEntidade'] . ' AND ';
            $stFiltro .= 'recurso.cod_receita_dados = ' . $arParametros['inCodReceitaDados'] . ' AND ';
            $stOrdem   = 'recurso.cod_recurso DESC';
            $rsRecursos = $this->recuperaMapeamento('TPPAReceitaRecurso', 'recuperaValoresRecurso', $stFiltro, $stOrdem, $boTransacao);

            # Formata os Recursos lidos para comparação com os novos Recursos.
            while (!$rsRecursos->eof()) {
                $arDados = array();
                $arDados['inCodRecurso'] = $rsRecursos->getCampo('cod_recurso');
                $arDados['stExercicio']  = $rsRecursos->getCampo('exercicio_recurso');
                $arDados['inCodConta']   = $rsRecursos->getCampo('cod_conta');
                $arDados['flTotal']      = $rsRecursos->getCampo('total');
                $arDados['flAno1']       = $rsRecursos->getCampo('ano1');
                $arDados['flAno2']       = $rsRecursos->getCampo('ano2');
                $arDados['flAno3']       = $rsRecursos->getCampo('ano3');
                $arDados['flAno4']       = $rsRecursos->getCampo('ano4');
                $arRecursosAntigos[] = $arDados;

                $rsRecursos->proximo();
            }
        }

        # Compara Recursos lidos com novos Recursos.
        foreach ($arRecursosNovos as $arCamposNovos) {
            $inCodRecurso = $arCamposNovos['inCodRecurso'];

            $arCamposAntigos = $this->encontraRecurso($arRecursosAntigos, $inCodRecurso);

            if ($arCamposAntigos && $arCamposNovos != $arCamposAntigos) {
                $obErro = $this->salvaRecurso('alteracao', $arParametros, $arCamposNovos, $boTransacao);
            } else {
                $obErro = $this->salvaRecurso('inclusao', $arParametros, $arCamposNovos, $boTransacao);
            }

            if ($obErro->ocorreu()) {
                return $obErro;
            }
        }

        # Procura recursos sobrando.
        foreach ($arRecursosAntigos as $arCamposVelhos) {
            $inCodRecurso = $arCamposVelhos['inCodRecurso'];

            $arCamposNovos = $this->encontraRecurso($arRecursosNovos, $inCodRecurso);

            if (!$arCamposNovos) {
                $obErro = $this->excluiRecurso($arParametros, $arCamposVelhos, $boTransacao);
            }

            if ($obErro->ocorreu()) {
                return $obErro;
            }
        }

        return $obErro;
    }

    /**
     * Inclui/Altera uma Receita e seus respectivos Recursos
     *
     * @author Marcio Medeiros
     * @param  array $arParametros global REQUEST
     * @return bool
     *
     * @todo Fazer merge com o método "salvaReceita"
     */
    public function salvarReceita($arParametros)
    {
        $this->validarDadosReceita($arParametros);
        if (!isset($this->boNovoRegistro)) {
            $this->erro = 'Valor de boNovoRegistro não informado para ' . __METHOD__;

            return false;
        }

        $boFlagTransacao = false;
        $boTransacao     = '';
        $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if ($this->boNovoRegistro) {
            // Inclusão
            $inCodReceita = 0;
            $this->obTPPAReceita->proximoCod($inCodReceita, $boTransacao);

            if ($inCodReceita == 0) {
                $this->erro = 'Erro ao gerar um codigo para Receita (' . __METHOD__ . ')';

                return false;
            }
            $this->obTPPAReceita->setDado('cod_receita', $inCodReceita);
            // Setar o índice de cod_receita para uso os metodos relacionados.
            $arParametros['inCodReceita'] = $inCodReceita;
        } else {
            //  Alteração
            $inCodReceita = (int) $arParametros['inCodReceita'];
            if ($inCodReceita == 0) {
                $this->erro = 'Codigo da Receita (PK cod_receita) não enviado para ' . __METHOD__;

                return false;
            }
            $this->obTPPAReceita->setDado('cod_receita', $inCodReceita);

        }

        if ($arParametros['inSizeRecurso'] == 0) {
            $this->erro = 'Necessário incluir pelo menos um recurso para esta receita';

            return false;
        }
        // Reordenar as chaves do array (são desordenadas quando há exclusão de item da lista)
        $this->reordenaValoresLista($arParametros);
        // Validar dados postados
        if (empty($arParametros['stExercicio'])) {
            $stExercicio = Sessao::read('exercicio');
            $arParametros['stExercicio'] = $stExercicio;
        } else {
            $stExercicio = $arParametros['stExercicio'];
        }

        $boPPAHomologado = $this->isPPAHomologado($arParametros['inCodPPA'], $boTransacao);

        if ($boPPAHomologado && empty($arParametros['inCodNorma'])) {
            $this->erro = 'O código da Norma deve ser informado!';

            return false;
        }

        # Calcula as novas Receitas.
        $flValorTotal = $this->calcularValorTotalRecursosReceita($arParametros);
        if (!$flValorTotal) {
            return false;
        }

        # Calcula a diferença entre Receita e Ação.
        $flValorTotalPPA = $this->recuperaValorTotalPPA($arParametros, $boTransacao);
        $flValorTotalReceita = $this->recuperaValorTotalReceita($arParametros['inCodPPA'], $arParametros['stExercicio'], '', $boTransacao);

        # Recupera dados já existentes de Receita para Alteração.
        if (!$this->boNovoRegistro) {
            $flValorReceita = $this->recuperaValorTotalReceita($arParametros['inCodPPA'], $arParametros['stExercicio'], $arParametros['inCodReceita'], $boTransacao);
        } else {
            $flValorReceita = 0.0;
        }

        $flDiferenca = $flValorTotal - $flValorReceita;
        $flTotal = $flValorTotalReceita - $flValorTotalPPA + $flDiferenca;

        # Testa se há Receitas o suficiente neste PPA para não ficar negativo.
        if ($flTotal < 0) {
            $this->erro = 'Valor Total de Receitas menor que o Total de Ações deste PPA!';

            return false;
        }

        $this->obTPPAReceita->setDado('cod_ppa', $arParametros['inCodPPA']);
        $this->obTPPAReceita->setDado('exercicio', $stExercicio);
        $this->obTPPAReceita->setDado('cod_conta', $arParametros['inCodConta']);
        $this->obTPPAReceita->setDado('cod_entidade', $arParametros['inCodEntidade']);
        $this->obTPPAReceita->setDado('valor_total', $flValorTotal);
        $this->obTPPAReceita->setDado('ativo', 't');
        $obErro = new Erro();
        if ($this->boNovoRegistro) {
            $this->buscarReceitaPPA($rsReceita, $arParametros, $boTransacao);
            $numRegistros = count($rsReceita->arElementos);
            if ($numRegistros > 0) {
                for ($i = 0; $i < $numRegistros; $i++) {
                    if ($rsReceita->arElementos[$i]['ativo'] == 't') {
                        $this->erro  = 'Receita (' . $arParametros['inCodConta'] . ')';
                        $this->erro .= ' já cadastrada para este PPA!';

                        return false;
                     }
                }
            }
            /**
            if (count($rsReceita->arElementos) > 0) {
                if ($rsReceita->arElementos[0]['ativo'] == 't') {
                    $this->erro  = 'Receita (' . $arParametros['inCodConta'] . ')';
                    $this->erro .= ' já cadastrada para este PPA!';

                    return false;
                }
            }
             */
            $obErro = $this->obTPPAReceita->inclusao($boTransacao);
        } else {
            $obErro = $this->obTPPAReceita->alteracao($boTransacao);
        }

        if ($obErro->ocorreu()) {
            $this->erro  = 'Erro ao salvar a Receita (' . $arParametros['inCodConta'] . ')!';
            $this->erro .= $obErro->getDescricao();

            return false;
        } else {
            return $this->salvarReceitaDados($arParametros, $boTransacao);
        }
    }

    /**
     * Exclui Recita Dados
     *
     * @param  array $arParametros
     * @param  bool  $boTransacao
     * @return bool
     * @ignore Atualizado para o Ticket #14344
     */
    private function excluirReceitaDados($arParametros, $boTransacao)
    {
        $stFiltro  = ' WHERE cod_receita = ' . $arParametros['inCodReceita'];
        $stFiltro .= ' AND cod_ppa       = ' . $arParametros['inCodPPA'];
        $stFiltro .= " AND exercicio     = '" . $arParametros['stExercicio'] . "'";
        $stFiltro .= ' AND cod_conta     = ' . $arParametros['inCodConta'];
        $stFiltro .= ' AND cod_entidade  = ' . $arParametros['inCodEntidade'];
        $rsRececeitaDados = $this->pesquisar('TPPAReceitaDados', 'recuperaTodos', $stFiltro, '', $boTransacao);
        $numReceitaDados = count($rsRececeitaDados->arElementos);
        for ($i=0; $i < $numReceitaDados; $i++) {
            $this->obTPPAReceitaDados->setDado('cod_receita',       $arParametros['inCodReceita']);
            $this->obTPPAReceitaDados->setDado('cod_ppa',           $arParametros['inCodPPA']);
            $this->obTPPAReceitaDados->setDado('exercicio',         $arParametros['stExercicio']);
            $this->obTPPAReceitaDados->setDado('cod_conta',         $arParametros['inCodConta']);
            $this->obTPPAReceitaDados->setDado('cod_entidade',      $arParametros['inCodEntidade']);
            $this->obTPPAReceitaDados->setDado('cod_receita_dados', $rsRececeitaDados->arElementos[$i]['cod_receita_dados']);
            $obErro = new Erro();
            $obErro = $this->obTPPAReceitaDados->exclusao();
            if ($obErro->ocorreu()) {
                $this->erro = 'Erro ao excluir os Dados da Receita: ';
                $this->erro .= $obErro->getDescricao();

                return false;
            }
        }

        return true;
    }

    /**
     * Salva os dados da receita.
     * Método para fins de histórico de inclusão e alterações.
     *
     * @param  array $arParametros
     * @param  bool  $boTransacao
     * @return bool
     * @ignore Atualizado para o ticket #14131
     */
    public function salvarReceitaDados($arParametros, $boTransacao)
    {
        // Buscar o ultimo valor do campo cod_receita_dados
        $this->obTPPAReceitaDados->proximoCod($inCodReceitaDados, $boTransacao);
        $arParametros['inCodReceitaDados'] = (int) $inCodReceitaDados;
        if ($arParametros['inCodReceitaDados'] == 0) {
            $this->erro = 'Erro ao gerar o codigo de Receita Dados!';

            return false;
        }
        $this->obTPPAReceitaDados->setDado('cod_receita',       $arParametros['inCodReceita']);
        $this->obTPPAReceitaDados->setDado('cod_ppa',           $arParametros['inCodPPA']);
        $this->obTPPAReceitaDados->setDado('exercicio',         $arParametros['stExercicio']);
        $this->obTPPAReceitaDados->setDado('cod_conta',         $arParametros['inCodConta']);
        $this->obTPPAReceitaDados->setDado('cod_entidade',      $arParametros['inCodEntidade']);
        $this->obTPPAReceitaDados->setDado('cod_receita_dados', $arParametros['inCodReceitaDados']);
        $boPPAHomologado = $this->isPPAHomologado($arParametros['inCodPPA'], $boTransacao);
        // Norma obrigatoria somente se PPA Homologado
        if ($boPPAHomologado) {
            if (empty($arParametros['inCodNorma'])) {
                $this->erro  = 'Codigo da norma nao informado em ' . __METHOD__;

                return false;
            }
            $this->obTPPAReceitaDados->setDado('cod_norma', $arParametros['inCodNorma']);
        }

        if ($this->obTPPAReceitaDados->inclusao()) {
            return $this->salvarReceitaRecurso($arParametros, $boTransacao);
        } else {
            $this->erro = 'Erro ao salvar os Dados da Receita!';

            return false;
        }
    }

    /**
     * Ação para incluir ou alterar uma Receita e seus respectivos
     * Recursos, versão sem transação própria.
     *
     * @author Pedro Vaz de Mello de Medeiros <pedro.medeiros@cnm.org.br>
     *
     * @param  string  $stAcao       especifica a ação a ser realizada
     * @param  array   $arParametros todos os parâmetros
     * @param  boolean $boTransacao  a transação atual, se houver
     * @return Erro    erro da transação, se houver
     *
     * @todo Fazer merge com o método "salvarReceita"
     */
    private function salvaReceita($stAcao, $arParametros, $boTransacao = '')
    {
        $obMReceita      = new TPPAReceita();
        $obMReceitaDados = new TPPAReceitaDados();

        $obErro = new Erro();

        # Valida todos os dados.
        if (!count($arParametros['arRecursos'])) {
            $obErro->setDescricao('Necessário incluir pelo menos um recurso para esta receita.');

            return $obErro;
        }

        # Usa ano de exercício atual se não houver.
        if (empty($arParametros['stExercicio'])) {
            $arParametros['stExercicio'] = Sessao::read('exercicio');
        }

        # Assegura os tipos corretos.
        $inCodEntidade  = (int) $arParametros['inCodEntidade'];
        $inCodConta     = (int) $arParametros['inCodConta'];
        $stExercicio    = $arParametros['stExercicio'];
        $inCodPPA       = (int) $arParametros['inCodPPA'];
        $inCodNorma     = (int) $arParametros['inCodNorma'];
        $boHomologado   = $this->isPPAHomologado($inCodPPA, $boTransacao);

        if ($boHomologado && $inCodNorma == 0) {
            $obErro->setDescricao('código da Norma deve ser informado');

            return $obErro;
        }

        if ($inCodEntidade == 0) {
            $obErro->setDescricao('código de entidade deve ser informado');

            return $obErro;
        }

        # Obtém valor total da Receita pela soma dos Recursos.
        $flValorTotal = 0;

        foreach ($arParametros['arRecursos'] as $arDados) {
            $flValorTotal += $arDados['flAno1'];
            $flValorTotal += $arDados['flAno2'];
            $flValorTotal += $arDados['flAno3'];
            $flValorTotal += $arDados['flAno4'];
            $flValorTotal += $arDados['flTotal'];
        }

        if ($flValorTotal == 0) {
            $obErro->setDescricao('o valor total dos Recursos não pode ser igual a zero');

            return $obErro;
        }

        # Verifica se já existe alguma receita com o mesmo código do PPA.
        if ($stAcao == 'inclusao') {
            $this->buscarReceitaPPA($rsReceita, $arParametros, $boTransacao);
            if (count($rsReceita->arElementos) > 0) {
                $obErro->setDescricao("Conta já cadastrada para PPA!($inCodPPA)");
                $obErro->setDescricao("Conta já cadastrada para PPA!($inCodPPA)");

                return $obErro;
            }
        }

        # Define os campos para inclusão na tabela ppa_receita.
        if ($stAcao == 'inclusao') {
            $obErro = $obMReceita->proximoCod($arParametros['inCodReceita'], $boTransacao);

            if ($obErro->ocorreu()) {
                return $obErro;
            }
        }

        $obMReceita->setDado('cod_receita', $arParametros['inCodReceita']);
        $obMReceita->setDado('cod_ppa', $inCodPPA);
        $obMReceita->setDado('exercicio', $stExercicio);
        $obMReceita->setDado('cod_conta', $inCodConta);
        $obMReceita->setDado('cod_entidade', $inCodEntidade);
        $obMReceita->setDado('valor_total', $flValorTotal);
        $obMReceita->setDado('ativo', 't');
        $obErro = $obMReceita->$stAcao($boTransacao);

        if ($obErro->ocorreu()) {
            return $obErro;
        }

        # Grava a norma vinculada a Receita
        if ($boHomologado) {
            $boOK = $this->salvarReceitaNorma($arParametros, $boTransacao);

            if (!$boOK) {
                $obErro->setDescricao('erro ao incluir Receita Norma');

                return $obErro;
            }
        }

        if ($stAcao == 'inclusao') {
            $obErro = $obMReceitaDados->proximoCod($arParametros['inCodReceitaDados'], $boTransacao);
        }

        if ($obErro->ocorreu()) {
            return $obErro;
        }

        # Define os campos para inclusão na tabela ppa_receita_dados.
        $obMReceitaDados->setDado('cod_receita_dados', $arParametros['inCodReceitaDados']);
        $obMReceitaDados->setDado('cod_receita', $arParametros['inCodReceita']);
        $obMReceitaDados->setDado('exercicio', $stExercicio);
        $obMReceitaDados->setDado('cod_conta', $inCodConta);
        $obMReceitaDados->setDado('cod_entidade', $inCodEntidade);
        $obMReceitaDados->setDado('cod_ppa', $inCodPPA);

        if ($boHomologado) {
            $obMReceitaDados->setDado('cod_norma', $inCodNorma);
        }

        $obErro = $obMReceitaDados->inclusao($boTransacao);

        if ($obErro->ocorreu()) {
            return $obErro;
        }

        return $this->salvaRecursos($stAcao, $arParametros, $boTransacao);
    }

    /**
     * Ação para incluir uma Receita e seus respectivos Recursos, versão sem
     * transação própria.
     *
     * @author Pedro Vaz de Mello de Medeiros <pedro.medeiros@cnm.org.br>
     * @param  array   $arParametros todos os parâmetros
     * @param  boolean $boTransacao  a transação atual, se houver
     * @return Erro    erro da transação, se houver
     */
    public function incluiReceita($arParametros, $boTransacao = '')
    {
        return $this->salvaReceita('inclusao', $arParametros, $boTransacao);
    }

    /**
     * Ação para alterar uma Receita e seus respectivos Recursos, versão sem
     * transação própria.
     *
     * @author Pedro Vaz de Mello de Medeiros <pedro.medeiros@cnm.org.br>
     *
     * @param  array   $arParametros todos os parâmetros
     * @param  boolean $boTransacao  a transação atual, se houver
     * @return Erro    erro da transação, se houver
     */
    public function alteraReceita($arParametros, $boTransacao = '')
    {
        return $this->salvaReceita('alteracao', $arParametros, $boTransacao);
    }

    /**
     * Reordena os itens da lista. O array de itens é
     * desordenado quando há exclusão de um item.
     *
     * @param  array $arParametros
     * @return void  $arParametros por referência
     */
    private function reordenaValoresLista(&$arParametros)
    {
        // Reordenar as chaves do array (são desordenadas quando há exclusão de item da lista)
        $arParametros['arTipoValor']   = array_values($arParametros['arTipoValor']);
        $arParametros['arCodRecurso']  = array_values($arParametros['arCodRecurso']);
        $arParametros['hdnNomRecurso'] = array_values($arParametros['hdnNomRecurso']);
        $arParametros['arValorTotal']  = array_values($arParametros['hdnArValorTotal']);
        $arParametros['arValorAno1']   = array_values($arParametros['hdnArValorAno1']);
        $arParametros['arValorAno2']   = array_values($arParametros['hdnArValorAno2']);
        $arParametros['arValorAno3']   = array_values($arParametros['hdnArValorAno3']);
        $arParametros['arValorAno4']   = array_values($arParametros['hdnArValorAno4']);
    }

    /**
     * Recupera o valor total de Despesa do PPA.
     *
     * @param  array $arParametros array contendo código do PPA
     * @param  bool  $boTransacao  se há transação ativa
     * @return float valor total de Despesa do PPA
     */
    public function recuperaValorTotalPPA($arParametros, $boTransacao = '')
    {
        $obRPPAManterAcao = new RPPAManterAcao();

        return $obRPPAManterAcao->recuperaTotalPPA($arParametros['inCodPPA'], $boTransacao);
    }

    /**
     * Exclui uma Receita (muda o status de ativo para FALSE)
     *
     * @param  array $arParametros global REQUEST
     * @return bool
     */
    public function excluirReceita($arParametros)
    {
        $this->validarDadosReceita($arParametros);
        $boFlagTransacao = false;
        $boTransacao     = '';
        $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        $obErro = new Erro();
        $this->obTPPAReceita->setDado('cod_receita',  $arParametros['inCodReceita']);
        $this->obTPPAReceita->setDado('cod_ppa',      $arParametros['inCodPPA']);
        $this->obTPPAReceita->setDado('exercicio',    $arParametros['stExercicio']);
        $this->obTPPAReceita->setDado('cod_conta',    $arParametros['inCodConta']);
        $this->obTPPAReceita->setDado('cod_entidade', $arParametros['inCodEntidade']);

        # Calcula a diferença entre Receita e Ação.
        $flValorTotalPPA = $this->recuperaValorTotalPPA($arParametros, $boTransacao);
        $flValorTotalReceita = $this->recuperaValorTotalReceita($arParametros['inCodPPA'], $arParametros['stExercicio'], '', $boTransacao);
        $flValorReceita = $this->recuperaValorTotalReceita($arParametros['inCodPPA'], $arParametros['stExercicio'], $arParametros['inCodReceita'], $boTransacao);
        $flDiferenca = $flValorTotal - $flValorReceita;
        $flTotal = $flValorTotalReceita - $flValorTotalPPA + $flDiferenca;

        # Testa se há Receitas o suficiente neste PPA para não ficar negativo.
        if ($flTotal < 0) {
            $this->erro = 'Valor Total de Receitas menor que o Total de Ações deste PPA!';

            return false;
        }

        if ($arParametros['inCodNorma'] > 0) { // Desativar a Receita
            $this->obTPPAReceita->setDado('valor_total', $flValorReceita);
            $this->obTPPAReceita->setDado('ativo', 'f');
            $obErro = $this->obTPPAReceita->alteracao($boTransacao);
            if ($obErro->ocorreu()) {
                $this->erro  = 'não foi possível mudar o status da Receita: ';
                $this->erro .= $obErro->getDescricao();

                return false;
            }
            // Gravar a norma
            $this->obTPPAReceitaInativaNorma->setDado('cod_receita' , $arParametros['inCodReceita']);
            $this->obTPPAReceitaInativaNorma->setDado('cod_ppa'     , $arParametros['inCodPPA']);
            $this->obTPPAReceitaInativaNorma->setDado('exercicio'   , $arParametros['stExercicio']);
            $this->obTPPAReceitaInativaNorma->setDado('cod_conta'   , $arParametros['inCodConta']);
            $this->obTPPAReceitaInativaNorma->setDado('cod_entidade', $arParametros['inCodEntidade']);
            $this->obTPPAReceitaInativaNorma->setDado('cod_norma'   , $arParametros['inCodNorma']);
            $obErro = $this->obTPPAReceitaInativaNorma->inclusao($boTransacao);
            if ($obErro->ocorreu()) {
                $this->erro  = 'não foi possivel incluir a Norma: ';
                $this->erro .= $obErro->getDescricao();

                return false;
            }
        } else {
            // Excluir Receita e respectivos vinculos (PPA não Homologado).
            if ($this->excluirReceitaRecurso($arParametros, $boTransacao)) {
                if ($this->excluirReceitaDados($arParametros, $boTransacao)) {
                    $obErro = $this->obTPPAReceita->exclusao($boTransacao);
                    if ($obErro->ocorreu()) {
                        $this->erro  = 'Erro ao excluir a Receita: ';
                        $this->erro .= $obErro->getDescricao();

                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTPPAReceita);

        return true;
    } // end function excluirReceita

    /**
    * Verifica se um PPA está homologado
    * Retorna true se o PPA estiver homologado
    *
    * @param int $inCodPPA
    * @return bool
    */
    public function isPPAHomologado($inCodPPA, $boTransacao = '')
    {
        $obRSNorma = new RecordSet;
        $stFiltro = " WHERE ppn.cod_ppa = $inCodPPA";
        $this->obTPPANorma->recuperaPPANorma($obRSNorma, $stFiltro, '', $boTransacao);
        if (count($obRSNorma->arElementos) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Recupera o valor total de uma ou todas as Receitas
     *
     * @return float
     */
    public function recuperaValorTotalReceita($inCodPPA, $stExercicio, $inCodReceita = '', $boTransacao = '')
    {
        $rsValorTotal = null;
        $stFiltro  = " WHERE cod_ppa = $inCodPPA                 \n";
        $stFiltro .= "   AND exercicio = '$stExercicio'          \n";
        $stFiltro .= "   AND ativo = 't'                         \n";

        if ($inCodReceita) {
            $stFiltro .= "   AND cod_receita = $inCodReceita     \n";
        }

        $this->obTPPAReceita->recuperaValorTotalReceita($rsValorTotal, $stFiltro, '', $boTransacao);

        return $rsValorTotal->arElementos[0]['valor_total'];
    }

    /**
     * Exclui os Recursos e seus respectivos valores
     *
     * @param  array $arParametros
     * @param  bool  $boTransacao
     * @return bool
     *
     * @ignore Atualizado para o Ticket #14344
     */
    private function excluirReceitaRecurso($arParametros, $boTransacao)
    {
        $stFiltro  = ' WHERE cod_receita  = ' . $arParametros['inCodReceita'];
        $stFiltro .= '   AND cod_ppa      = ' . $arParametros['inCodPPA'];
        $stFiltro .= "   AND exercicio    = '" . $arParametros['stExercicio'] . "'";
        $stFiltro .= '   AND cod_conta    = ' . $arParametros['inCodConta'];
        $stFiltro .= '   AND cod_entidade = ' . $arParametros['inCodEntidade'];
        $rsRececeitaRecursos = $this->pesquisar('TPPAReceitaRecurso', 'recuperaTodos', $stFiltro, '', $boTransacao);
        $numRecursos = count($rsRececeitaRecursos->arElementos);
        if ($numRecursos > 0) {
            $obErro = new Erro;
            for ($i = 0; $i < $numRecursos; $i++) {
                $stExercicioRecurso = $rsRececeitaRecursos->arElementos[$i]['exercicio_recurso'];
                $inCodRecurso       = $rsRececeitaRecursos->arElementos[$i]['cod_recurso'];
                $this->obTPPAReceitaRecursoValor->setDado('cod_receita',       $arParametros['inCodReceita']);
                $this->obTPPAReceitaRecursoValor->setDado('cod_ppa',           $arParametros['inCodPPA']);
                $this->obTPPAReceitaRecursoValor->setDado('exercicio',         $stExecicio);
                $this->obTPPAReceitaRecursoValor->setDado('cod_conta',         $arParametros['inCodConta']);
                $this->obTPPAReceitaRecursoValor->setDado('cod_entidade',      $arParametros['inCodEntidade']);
                $this->obTPPAReceitaRecursoValor->setDado('exercicio_recurso', $stExercicioRecurso);
                $this->obTPPAReceitaRecursoValor->setDado('cod_recurso',       $inCodRecurso);
                // Excluir valores do recurso
                $obErro = $this->obTPPAReceitaRecursoValor->exclusao($boTransacao);
                if ($obErro->ocorreu()) {
                    $stErro  = "Erro ao excluir os valores do Recurso ($inCodRecurso): ";
                    $stErro .= $obErro->getDescricao();
                    $this->erro = $stErro;

                    return false;
                } else {
                    // Excluir o Recurso
                    $this->obTPPAReceitaRecurso->setDado('cod_receita',       $arParametros['inCodReceita']);
                    $this->obTPPAReceitaRecurso->setDado('cod_ppa',           $arParametros['inCodPPA']);
                    $this->obTPPAReceitaRecurso->setDado('exercicio',         $arParametros['stExercicio']);
                    $this->obTPPAReceitaRecurso->setDado('cod_conta',         $arParametros['inCodConta']);
                    $this->obTPPAReceitaRecurso->setDado('cod_entidade',      $arParametros['inCodEntidade']);
                    $this->obTPPAReceitaRecurso->setDado('exercicio_recurso', $stExercicioRecurso);
                    $this->obTPPAReceitaRecurso->setDado('cod_recurso',       $inCodRecurso);
                    $obErro = $this->obTPPAReceitaRecurso->exclusao($boTransacao);
                    if ($obErro->ocorreu()) {
                        $this->erro  = "Erro ao excluir o Recurso ($inCodRecurso): ";
                        $this->erro .= $obErro->getDescricao();

                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
    * Calcula o valor total dos recursos da Receita
    *
    * @param array $arParametros
    * @return int Valor total da Receita
    */
    private function calcularValorTotalRecursosReceita($arParametros)
    {
        $flValorTotalRecursos = 0;
        $flValorTotal         = 0;
        for ($i=0; $i < $arParametros['inSizeRecurso']; $i++) {
            if (strtolower($arParametros['arTipoValor'][$i]) == 'total') {
                $stValorTotal = $arParametros['arValorTotal'][$i];
                if (empty($stValorTotal)) {
                    $stValorTotal = 0;
                } elseif (strpos($stValorTotal, ",") > 0) {
                    $stValorTotal = str_replace('.', '', $stValorTotal);
                    $stValorTotal = str_replace(',', '.', $stValorTotal);
                }
                if (strlen($stValorTotal) > 14) {
                    $this->erro = 'Valor Total "'.$arParametros['arValorTotal'][$i].'" ultrapassa o máximo permitido de "99.999.999.999.999,99"';

                    return false;
                }
                $flValorTotal += (float) $stValorTotal;
            } else { // Valor por ano: somar os 4 anos
                for ($iAno = 1; $iAno <= 4; $iAno++) {
                    $stValorAno = $arParametros["arValorAno{$iAno}"][$i];
                    if (empty($stValorAno)) {
                        $stValorAno = 0;
                    } elseif (strpos($stValorAno, ",") > 0) {
                        $stValorAno = str_replace('.', '', $stValorAno);
                        $stValorAno = str_replace(',', '.', $stValorAno);
                    }
                    if (strlen($stValorAno) > 14) {
                        $this->erro = 'Valor "'.$arParametros["arValorAno{$iAno}"][$i].'" do ano '.$iAno.' ultrapassa o máximo permitido de "99.999.999.999.999,99"';

                        return false;
                    }
                    $flValorTotal += (float) $stValorAno;
                }
            }
        } // end for inSizeRecurso
        if ($flValorTotal == 0) {
            $this->erro = 'O valor total do Recurso não pode ser igual a zero';

            return false;
        }

        return $flValorTotal;
    }

    /**
     * Verifica se já existe um ppa com o mesmo código do que
     * está sendo incluído pelo método incluirReceita
     *
     * @param  RecordSet $rsReceitaPPA
     * @param  array     $arParametros
     * @param  bool      $boTransacao
     * @return RecordSet $rsReceitaPPA
     */
    private function buscarReceitaPPA(&$rsReceitaPPA, array $arParametros, $boTransacao = '')
    {
        if (empty($arParametros['inCodPPA']) || empty($arParametros['stExercicio']) || empty($arParametros['inCodConta'])) {
            return SistemaLegado::exibeAviso(urlencode('não foi possível pesquisar o vínculo da Receita com o PPA'), 'n_'.$arParametros['stAcao'], 'erro');
        }
        $rsReceitaPPA = null;
        $stFiltro  = ' WHERE cod_ppa   = ' . $arParametros['inCodPPA'];
        $stFiltro .= '   AND exercicio = ' . $arParametros['stExercicio'];
        $stFiltro .= '   AND cod_conta = ' . $arParametros['inCodConta'];
        $this->obTPPAReceita->recuperaReceitaPPA($rsReceitaPPA, $stFiltro, '', $boTransacao);
    }

    /**
     * Salva os recursos da Receita PPA
     *
     * @param  array $arParametros
     * @param  bool  $boTransacao
     * @return bool
     */
    private function salvarReceitaRecurso($arParametros, $boTransacao)
    {
        // Gravar recursos da lista
        for ($i = 0; $i < $arParametros['inSizeRecurso']; $i++) {
            $inCodRecurso = (int) $arParametros['arCodRecurso'][$i];
            if ($inCodRecurso == 0) {
                $this->erro  = 'Erro ao salvar o Recurso: ';
                $this->erro .= 'código do Recurso não informado na lista de Recursos!';

                return false;
            }
            $this->obTPPAReceitaRecurso->setDado('cod_receita', $arParametros['inCodReceita']);
            $this->obTPPAReceitaRecurso->setDado('cod_ppa', $arParametros['inCodPPA']);
            $this->obTPPAReceitaRecurso->setDado('exercicio', $arParametros['stExercicio']);
            $this->obTPPAReceitaRecurso->setDado('cod_conta', $arParametros['inCodConta']);
            $this->obTPPAReceitaRecurso->setDado('cod_entidade', $arParametros['inCodEntidade']);
            $this->obTPPAReceitaRecurso->setDado('cod_receita_dados', $arParametros['inCodReceitaDados']);
            // Buscar exercicio do Recurso
            $stExercicioRecurso = $this->buscarExercicioRecurso($inCodRecurso);
            $this->obTPPAReceitaRecurso->setDado('exercicio_recurso', $stExercicioRecurso);
            $this->obTPPAReceitaRecurso->setDado('cod_recurso', $inCodRecurso);
            // Incluir Recurso
            $obErro = $this->obTPPAReceitaRecurso->inclusao($boTransacao);
            if ($obErro->ocorreu()) {
                $this->erro  = 'Erro ao incluir o Recurso da Receita!';

                return false;
            }
        } // end for
        // Gravar os valores de cada Recurso
        return $this->salvarReceitaRecursoValor($arParametros, $boTransacao);
    }

    /**
     * Busca o exercicio do Recurso em orcamento.recurso
     *
     * @param  int    $inCodRecurso
     * @return string
     */
    private function buscarExercicioRecurso($inCodRecurso)
    {
        $obRsExercicioRercurso = null;
        $stCondicao = ' AND recurso.cod_recurso = ' . $inCodRecurso;
        $this->obTOrcamentoRecurso->recuperaBuscaRecurso($obRsExercicioRercurso, $stCondicao , '' , $boTransacao);

        return $obRsExercicioRercurso->arElementos[0]['exercicio'];
    }

    /**
     * Grava a norma da Receita (Quando PPA Homologado)
     *
     * @param  array  $arParametros
     * @param  string $boTransacao
     * @return bool
     */
    private function salvarReceitaNorma($arParametros, $boTransacao)
    {
        $this->obTPPAReceitaInativaNorma->setDado('cod_norma', $arParametros['inCodNorma']);
        $this->obTPPAReceitaInativaNorma->setDado('cod_ppa', $arParametros['inCodPPA']);
        $this->obTPPAReceitaInativaNorma->setDado('cod_receita', $arParametros['inCodReceita']);
        $this->obTPPAReceitaInativaNorma->setDado('exercicio', $arParametros['stExercicio']);
        $this->obTPPAReceitaInativaNorma->setDado('cod_conta', $arParametros['inCodConta']);
        $obErro = $this->obTPPAReceitaInativaNorma->inclusao($boTransacao);
        if ($obErro->ocorreu()) {
            $this->erro  = 'Erro ao incluir a Norma da Receita()';

            return false;
        }

        return true;
    }

    /**
     * Grava os valores dos recursos da Receita
     *
     * @param  array $arParametros global REQUEST
     * @return bool
     * @ignore Atualizado para o Ticket #14553
     */
    private function salvarReceitaRecursoValor($arParametros, $boTransacao)
    {
        for ($i=0; $i < $arParametros['inSizeRecurso']; $i++) {
            $inCodRecurso = (int) $arParametros['arCodRecurso'][$i];
            $stExercicioRecurso = $this->buscarExercicioRecurso($inCodRecurso);
            for ($inAno = 0; $inAno <= 4; $inAno++) {
                if (strtolower($arParametros['arTipoValor'][$i]) == 'total') {
                    if ($inAno == 0) {
                        $stValorAno = $arParametros['arValorTotal'][$i];
                    } else {
                        $stValorAno = '0,00';
                    }
                } else {
                    $stValorAno = $arParametros["arValorAno{$inAno}"][$i];
                }

                if (strpos($stValorAno, ",") > 0) {
                    $stValorAno = str_replace('.', '', $stValorAno);
                    $stValorAno = str_replace(',', '.', $stValorAno);
                }
                $flValorAno = number_format($stValorAno, 2, '.', '');
                $this->obTPPAReceitaRecursoValor->setDado('cod_receita',       $arParametros['inCodReceita']);
                $this->obTPPAReceitaRecursoValor->setDado('cod_ppa',           $arParametros['inCodPPA']);
                $this->obTPPAReceitaRecursoValor->setDado('exercicio',         $arParametros['stExercicio']);
                $this->obTPPAReceitaRecursoValor->setDado('cod_conta',         $arParametros['inCodConta']);
                $this->obTPPAReceitaRecursoValor->setDado('cod_entidade',      $arParametros['inCodEntidade']);
                $this->obTPPAReceitaRecursoValor->setDado('cod_receita_dados', $arParametros['inCodReceitaDados']);
                $this->obTPPAReceitaRecursoValor->setDado('exercicio_recurso', $stExercicioRecurso);
                $this->obTPPAReceitaRecursoValor->setDado('cod_recurso', $inCodRecurso);
                $this->obTPPAReceitaRecursoValor->setDado('ano', $inAno);
                $this->obTPPAReceitaRecursoValor->setDado('valor', $flValorAno);
                $obErro = $this->obTPPAReceitaRecursoValor->inclusao($boTransacao);
                if ($obErro->ocorreu()) {
                    $this->erro  = 'Erro ao incluir o valor por Ano do Recurso: ';
                    $this->erro .= $obErro->getDescricao();

                    return false;
                }

            } // end for
        } // end for
        $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTPPAReceitaRecursoValor);

        return true;
    }

    /**
     * Verifica se um PPA está cadastrado
     *
     * @param  int $inCodPPA
     * @return int numero de registro encontrados
     */
    public function isPPACadastrado($inCodPPA)
    {
        $obTPPA = new TPPA();
        $rsPPA  = new Recordset;
        $stFiltro = " WHERE cod_ppa = $inCodPPA";
        $obTPPA->recuperaPPA($rsPPA, '', $stFiltro);

        return count($rsPPA->arElementos);
    }

    /**
     * Verifica se existe destinação de Recurso do PPA em questão.
     *
     * @param  int  $inCodPPA
     * @param  bool $boTransacao
     * @return bool
     */
    public function pesquisarDestinacaoRecurso($inCodPPA, $boTransacao = '')
    {
        $stFiltro = ' WHERE cod_ppa = ' . $inCodPPA;
        $rsPPA = $this->pesquisar('TPPA', 'recuperaTodos', $stFiltro, '', $boTransacao);
        $boDestinacao = false;

        if (!$rsPPA->eof()) {
            $boDestinacao = $rsPPA->getCampo('destinacao_recurso') == 't' ? true : false;
        }

        return $boDestinacao;
    }

}
?>
