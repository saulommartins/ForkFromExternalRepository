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
    * Classe Bens, é feito todos os cadastros e todas as saídas relacionadas a bens
    * Data de Criação   : 24/03/2003

    * @author Desenvolvedor Alessandro La-Rocca Silveira
    * @author Desenvolvedor Ricardo Lopes de Alencar

    * @ignore

    $Revision: 18445 $
    $Name$
    $Autor: $
    $Date: 2006-12-01 13:55:29 -0200 (Sex, 01 Dez 2006) $

    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.35  2006/12/01 15:55:29  hboaventura
bug #7716#

Revision 1.34  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.33  2006/07/06 12:11:27  diego

*/
//include_once ("../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php");

class bens
{
    /*** Declaração das variáveis de classe ***/
    public $codigo;
    public $dtAgendamento;
    public $numCgm;
    public $nomCgm;
    public $dtRealizacao;
    public $dtGarantia;
    public $observacao;
    public $notaFiscal;
    public $descricao;
    public $nomeSituacao;
    public $codSit;
    public $orgao;
    public $unidade;
    public $departamento;
    public $setor;
    public $local;
    public $natureza;
    public $grupo;
    public $especie;
    public $codEmpenho;
    public $numNotaFiscal;
    public $exercicioEmpenho;
    public $numPlaca;
    public $entidade;
    public $valor;
    public $codEntidade;

    /*** Método Construtor ***/
    public function bens()
    {
        $this->codigo = "";
        $this->dtAgendamento = "";
        $this->numCgm = "";
        $this->nomCgm = "";
        $this->dtRealizacao = "";
        $this->dtGarantia = "";
        $this->observacao = "";
        $this->notaFiscal = "";
        $this->descricao = "";
        $this->nomeSituacao = "";
        $this->codSit = "";
        $this->orgao = "";
        $this->unidade = "";
        $this->departamento = "";
        $this->setor = "";
        $this->local = "";
        $this->natureza = "";
        $this->grupo = "";
        $this->especie = "";
        $this->codEmpenho = "";
        $this->numNotaFiscal = "";
        $this->exercicioEmpenho = "";
        $this->numPlaca = "";
        $this->entidade= "";
        $this->valor="";
        $this->codEntidade="";
    }

    /*** Método que seta variáveis ***/
    public function setaVariaveis($cod, $dataAgenda, $cgm, $dataRealiza = "", $dataGarantia = "", $obs, $emp = "",
    $nota = "", $val = "", $exercicioEmpenho = "", $numPlaca = "", $codEntidade = "") {
        $this->codigo = $cod;
        $this->dtAgendamento = dataToSql($dataAgenda);
        $this->numCgm = $cgm;
        if ($dataRealiza) {
            $this->dtRealizacao = datatoSql($dataRealiza);
        }
        if ($dataGarantia) {
            $this->dtGarantia = dataToSql($dataGarantia);
        }
        $this->observacao = $obs;
        $this->codEmpenho = $emp;
        $this->notaFiscal = $nota;
        $this->exercicioEmpenho = $exercicioEmpenho;
        $this->numPlaca = $numPlaca;
        $this->entidade = $codEntidade;
        $this->valor    = $val;
        $this->codEntidade    = $codEntidade;
    }

    /*** Método que compara o código e a data de Agendamento selecionada
    com a base em busca de data igual ***/
    public function comparaData()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "SELECT
                        dt_agendamento
                    FROM
                        patrimonio.manutencao
                    WHERE
                            cod_bem        = $this->codigo
                        AND dt_agendamento = '$this->dtAgendamento'";

        $dbConfig->abreSelecao( $select );
        $result = $dbConfig->pegaCampo("dt_agendamento");
        $dbConfig->limpaSelecao();
        if ($result == "") {
            $dbConfig->fechaBd();

            return true;
        } else {
            $dbConfig->fechaBd();

            return false;
        }
    }

    /*** Método que compara o código do bem informado com a tabela patrimonio.bem.**/
    public function comparaCodigoBem()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "select cod_bem
                        from patrimonio.bem
                        where cod_bem = '$this->codigo'";
        //echo $select."<br>";
        $dbConfig->abreSelecao($select);
        $result = $dbConfig->pegaCampo("cod_bem");
        $dbConfig->limpaSelecao();
        if ($result != "") {
            $dbConfig->fechaBd();

            return true;
        } else {
            $dbConfig->fechaBd();

            return false;
        }
    }

    /*** Método que compara o Código do CGM com a tabela CGM ***/
    public function comparaNumCgm()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "select numcgm
                        from sw_cgm
                        where numcgm = '$this->numCgm'";
        ///echo $select."<br>";
        $dbConfig->abreSelecao($select);
        $result = $dbConfig->pegaCampo("numcgm");
        $dbConfig->limpaSelecao();
        if ($result != "") {
            $dbConfig->fechaBd();

            return true;
        } else {
            $dbConfig->fechaBd();

            return false;
        }
    }

    /*** Método que inclui Agendamento de Manutenção ***/
    public function incluiAgendamento()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert =   "INSERT INTO patrimonio.manutencao (
                        cod_bem, dt_agendamento, numcgm, observacao
                    )
                    VALUES (
                        $this->codigo, '$this->dtAgendamento', $this->numCgm, '$this->observacao'
                    );";

        if ($dbConfig->executaSql($insert)) {
            $dbConfig->fechaBd();

            return true;
        } else {
            $dbConfig->fechaBd();

            return false;
        }
    }

    /*** Método que lista os agendamentos pendentes ***/
    public function listaAgendamento($codbem)
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "select cod_bem, dt_agendamento
                    from patrimonio.manutencao
                    where cod_bem = $codbem
                    and dt_realizacao is NULL";
        //echo $select."<br>";
        $dbConfig->abreSelecao($select);
        while (!$dbConfig->eof()) {
            $lista[] = $dbConfig->pegaCampo("dt_agendamento");
            $dbConfig->vaiProximo();
        }
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();

        return $lista;
    }

    /*** Método que altera agendamento ***/
    public function alteraAgendamento($dataAntiga)
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $update =   "UPDATE
                        patrimonio.manutencao
                    SET
                        dt_agendamento = '$this->dtAgendamento',
                        observacao     = '$this->observacao',
                        numcgm         = $this->numCgm
                    WHERE
                        cod_bem        = $this->codigo
                    AND dt_agendamento = '$dataAntiga';";
        $result = $dbConfig->executaSql($update);
        if ($result) {
            $dbConfig->fechaBd();

            return true;
        } else {
            $dbConfig->fechaBd();

            return false;
        }
    }

    /*** Método que inclui manutenção de bens ***/
    public function incluiManutencao()
    {
        /*$dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $verificaEmpenho = "SELECT
                                *
                            FROM
                                empenho.empenho
                            WHERE
                                    cod_empenho = $this->codEmpenho
                                AND exercicio  = '$this->exercicioEmpenho' ;";
        $dbConfig->abreSelecao($verificaEmpenho);
        while (!$dbConfig->eof()) {
            $cod = $dbConfig->pegaCampo("cod_empenho");
            $lista[] = $dbConfig->pegaCampo("exercicio");
            $dbConfig->vaiProximo();
        }
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();*/
        //if (!is_null($lista)) {
            if ($this->dtGarantia != "") {
                $sqlDtGarantia = "dt_garantia   = '".$this->dtGarantia."',";
            }
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            $this->valor = ($this->valor == "" | $this->valor == null)?"0":$this->valor;
            $update  = "UPDATE
                            patrimonio.manutencao
                       SET
                            numcgm        = $this->numCgm,
                            dt_realizacao = '$this->dtRealizacao',
                            $sqlDtGarantia
                            observacao    = '$this->observacao'
                     WHERE
                                cod_bem        = $this->codigo
                            AND dt_agendamento = '$this->dtAgendamento'; ";

            $update .= "INSERT INTO patrimonio.manutencao_paga (
                            cod_bem, dt_agendamento, cod_entidade, cod_empenho, exercicio, valor
                        )
                        VALUES (
                            $this->codigo, '$this->dtAgendamento', $this->entidade, ";
            $update .= ( $this->codEmpenho == 0 ) ? "NULL," : "$this->codEmpenho,";
            $update .= "'$this->exercicioEmpenho', $this->valor
                        ); ";
            if ( $dbConfig->executaSql($update) ) {
                $dbConfig->fechaBd();

                return ;
            } else {
                $dbConfig->fechaBd();

                return "Bem: $this->codigo";
            }
        /*} else {
            return "Empenho: $this->codEmpenho - $this->exercicioEmpenho não está cadastrado.";
        }*/
    }

    /*** Método que lista todas as manutenções para serem alteradas ou excluidas ***/
    public function listaManutencao()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "SELECT
                            m.cod_bem, m.dt_agendamento
                    FROM
                        patrimonio.manutencao as m
                    WHERE
                        dt_realizacao IS NOT NULL
                        AND cod_bem = '$this->codigo'";
        //echo $select."<br>";
        $dbConfig->abreSelecao($select);
        while (!$dbConfig->eof()) {
            $cod = $dbConfig->pegaCampo("cod_bem");
            $lista[] = $dbConfig->pegaCampo("dt_agendamento");
            $dbConfig->vaiProximo();
        }
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();

        return $lista;
    }

    /*** Método que mostra a manutenção selecionada ***/
    public function mostraManutencao($cod, $data)
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "SELECT
                        C.nom_cgm,
                        B.descricao,
                        M.*,
                        MP.cod_entidade,
                        MP.cod_empenho,
                        MP.exercicio,
                        MP.valor
                     FROM
                        patrimonio.manutencao      as M,
                        patrimonio.manutencao_paga as MP,
                        sw_cgm             as C,
                        patrimonio.bem             as B
                    WHERE
                            M.cod_bem         = '$cod'
                        AND B.cod_bem         = M.cod_bem
                        AND M.dt_agendamento  = '$data'
                        AND C.numcgm          = M.numcgm
                        AND M.cod_bem         = MP.cod_bem
                        AND MP.dt_agendamento = M.dt_agendamento";
        $dbConfig->abreSelecao( $select );
        $this->codigo           = $dbConfig->pegaCampo("cod_bem");
        $this->descricao        = $dbConfig->pegaCampo("descricao");
        $this->dtAgendamento    = $dbConfig->pegaCampo("dt_agendamento");
        $this->nomeCgm          = $dbConfig->pegaCampo("nom_cgm");
        $this->numCgm           = $dbConfig->pegaCampo("numcgm");
        $this->dtRealizacao     = $dbConfig->pegaCampo("dt_realizacao");
        $this->dtGarantia       = $dbConfig->pegaCampo("dt_garantia");
        $this->observacao       = $dbConfig->pegaCampo("observacao");
        $this->codEmpenho          = $dbConfig->pegaCampo("cod_empenho");
        $this->entidade         = $dbConfig->pegaCampo("cod_entidade");
        $this->exercicioEmpenho = $dbConfig->pegaCampo("exercicio");
        $this->valor            = $dbConfig->pegaCampo("valor");

        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();
    }

    /*** Método que mostra a manutenção agendada ***/
    public function mostraManutencaoAgendada($cod, $data)
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
  /*      $select   = "SELECT
                        M.*,
                        C.nom_cgm,
                        MP.cod_empenho,
                        MP.exercicio
                    FROM
                        patrimonio.manutencao      as M,
                        sw_cgm             as C,
                        patrimonio.manutencao_paga as MP
                    WHERE
                            M.cod_bem    = $cod
                        AND M.dt_agendamento = '$data'
                        AND M.numcgm         = C.numcgm
                        AND M.cod_bem        = MP.cod_bem
                        AND M.dt_agendamento = MP.dt_agendamento
                    ";*/

        $select =" SELECT
                    M.cod_bem,
                    B.descricao,
                    M.dt_agendamento,
                    M.numcgm,
                    M.dt_garantia,
                    M.dt_realizacao,
                    M.observacao,
                    C.nom_cgm
                FROM
                    patrimonio.manutencao as M,
                    patrimonio.bem as B,
                    sw_cgm as C
                WHERE
                    M.cod_bem = $cod              AND
                    M.cod_bem = B.cod_bem      AND
                    M.dt_agendamento = '$data' AND
                    M.numcgm = C.numcgm                  ";
        $dbConfig->abreSelecao($select);
        $this->codigo           = $dbConfig->pegaCampo("cod_bem"        );
        $this->descricao        = $dbConfig->pegaCampo("descricao"      );
        $this->dtAgendamento    = $dbConfig->pegaCampo("dt_agendamento" );
        $this->numCgm           = $dbConfig->pegaCampo("numcgm"         );
        $this->nomCgm           = $dbConfig->pegaCampo("nom_cgm"        );
        $this->observacao       = $dbConfig->pegaCampo("observacao"     );
        $this->notaFiscal    = "";
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();
    }

/*** Método que altera os dados da manutenção dos bens ***/
    public function alteraManutencao($cod)
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $update =   "update patrimonio.manutencao
                    set cod_bem = $this->codigo,
                    dt_agendamento = '$this->dtAgendamento',
                    numcgm = $this->numCgm,
                    dt_realizacao = '$this->dtRealizacao',
                    dt_garantia = '$this->dtGarantia',
                    observacao = '$this->observacao'
                    where cod_bem = '$cod'
                    and dt_agendamento = '$this->dtAgendamento'";
        //echo $update."<br>";
        //echo $update2."<br>";
        $result = $dbConfig->executaSql($update);
        $result2 = $dbConfig->executaSql($update2);
        if ($result && $update2) {
            $dbConfig->fechaBd();

            return true;
        } else {
            $dbConfig->fechaBd();

            return false;
        }
    }

    /*** Método que Exclui Manutenção ***/
    public function excluiManutencao()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $delete =  "DELETE FROM
                        patrimonio.manutencao_paga
                    WHERE
                        cod_bem        = '$this->codigo'
                    AND dt_agendamento = '$this->dtAgendamento'";
        $delete2 =  "DELETE FROM
                        patrimonio.manutencao
                    WHERE
                        cod_bem        = '$this->codigo'
                    AND dt_agendamento = '$this->dtAgendamento'";
        $result  = $dbConfig->executaSql($delete);
        $result2 = $dbConfig->executaSql($delete2);
        if ($result && $result2) {
            $dbConfig->fechaBd();

            return true;
        } else {
            $dbConfig->fechaBd();

            return false;
        }
    }

    /*** Método que seleciona o bem que terá a situação alterada ***/
    public function selecionaBem()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "SELECT * FROM patrimonio.bem WHERE cod_bem = '$this->codigo'";
        $dbConfig->abreSelecao($select);
        $this->descricao = $dbConfig->pegaCampo("descricao");
        $this->numPlaca  = $dbConfig->pegaCampo("num_placa");
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();

        return $this->descricao;
    }

    /*** Método que seleciona o historico do bem ***/
    public function selecionaHistorico()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select = "  select historico.cod_bem
                          , orgao.nom_orgao
                          , unidade.nom_unidade
                          , depart.nom_departamento
                          , setor.nom_setor
                          , local.nom_local
                      from patrimonio.historico_bem as historico
                         , administracao.orgao              as orgao
                         , administracao.unidade            as unidade
                         , administracao.departamento       as depart
                         , administracao.setor              as setor
                         , administracao.local              as local
                     where historico.cod_bem       = '$this->codigo'
                       and orgao.cod_orgao         = historico.cod_orgao
                       and orgao.ano_exercicio     = historico.ano_exercicio
                       and unidade.cod_unidade     = historico.cod_unidade
                       and unidade.cod_orgao       = historico.cod_orgao
                       and unidade.ano_exercicio   = historico.ano_exercicio
                       and depart.cod_departamento = historico.cod_departamento
                       and depart.cod_unidade      = historico.cod_unidade
                       and depart.cod_orgao        = historico.cod_orgao
                       and depart.ano_exercicio    = historico.ano_exercicio
                       and setor.cod_setor         = historico.cod_setor
                       and setor.cod_departamento = historico.cod_departamento
                       and setor.cod_unidade      = historico.cod_unidade
                       and setor.cod_orgao        = historico.cod_orgao
                       and setor.ano_exercicio    = historico.ano_exercicio
                       and local.cod_local         = historico.cod_local
                       and local.cod_setor         = historico.cod_setor
                       and local.cod_departamento = historico.cod_departamento
                       and local.cod_unidade      = historico.cod_unidade
                       and local.cod_orgao        = historico.cod_orgao
                       and local.ano_exercicio    = historico.ano_exercicio ";

        //echo $select."<br>";
        $dbConfig->abreSelecao($select);
        $this->orgao = $dbConfig->pegaCampo("nom_orgao");
        $this->unidade = $dbConfig->pegaCampo("nom_unidade");
        $this->departamento = $dbConfig->pegaCampo("nom_departamento");
        $this->setor = $dbConfig->pegaCampo("nom_setor");
        $this->local = $dbConfig->pegaCampo("nom_local");
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();
    }

    /*** Método que seleciona a classificação do bem ***/
    public function selecionaClassificacao()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "select natureza.nom_natureza, grupo.nom_grupo, especie.nom_especie
                        from patrimonio.natureza as natureza, patrimonio.grupo as grupo, patrimonio.especie as especie, patrimonio.bem_atributo_especie as  bemAtributoEspecie  where bemAtributoEspecie.cod_bem = '$this->codigo'
                        and natureza.cod_natureza = bemAtributoEspecie.cod_natureza
                        and grupo.cod_grupo = bemAtributoEspecie.cod_grupo
                        and especie.cod_especie = bemAtributoEspecie.cod_especie";
        //echo $select."<br>";
        $dbConfig->abreSelecao($select);
        $this->natureza = $dbConfig->pegaCampo("nom_natureza");
        $this->grupo = $dbConfig->pegaCampo("nom_grupo");
        $this->especie = $dbConfig->pegaCampo("nom_especie");
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();
    }

    /*** Método que seleciona a situação atual do bem ***/
    public function selecionaSituacao()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "select sit.cod_situacao, sit.nom_situacao
                        from patrimonio.situacao_bem as sit, patrimonio.historico_bem as hist
                        where hist.cod_bem = $this->codigo
                        and sit.cod_situacao = hist.cod_situacao";
        //echo $select."<br>";
        $dbConfig->abreSelecao($select);
        $this->nomeSituacao = $dbConfig->pegaCampo("nom_situacao");
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();

        return $this->nomeSituacao;
    }

    /*** Método que seleciona todas as situações de bens ***/
    public function listaSituacao()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "select cod_situacao, nom_situacao
                    from patrimonio.situacao_bem ";
        //echo $select."<br>";
        $dbConfig->abreSelecao($select);
        while (!$dbConfig->eof()) {
            $cod = $dbConfig->pegaCampo("cod_situacao");
            $lista[$cod] = $dbConfig->pegaCampo("nom_situacao");
            $dbConfig->vaiProximo();
        }
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();

        return $lista;
    }

    /*** Método que altera a Situação do bem ***/
    public function alteraSituacao()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $update =   "update patrimonio.historico_bem set
                        cod_situacao = '".$this->codSit."',
                        timestamp = now()
                        where cod_bem = '".$this->codigo."'";
        //echo $update."<br>";
        $result = $dbConfig->executaSql($update);
        if ($result) {
            $dbConfig->fechaBd();

            return true;
        } else {
            $dbConfig->fechaBd();

            return false;
        }
    }

/**************************************************************************
Inclui um novo bem no banco de dados
/**************************************************************************/
    public function incluirBem($codBem, $descricao, $detalhamento,$dtDepreciacao, $dtAquisicao, $dtDepreciacao, $dtGarantia,
    $valorBem, $valorDepreciacao, $identificacao, $situacao, $descSituacao, $local, $anoExLocal, $fornecedor,
    $atributos, $natureza, $grupo, $especie, $codEmpenho, $numNotaFiscal, $exercicioEmpenho, $numPlaca, $codEntidade, $iLoteQtde = 1) {

        //Pega os dados do local
        $vetLocal   = preg_split( "/[^a-zA-Z0-9]/", $local );
        $codOrgao   = $vetLocal[0];
        $codUnidade = $vetLocal[1];
        $codDpto    = $vetLocal[2];
        $codSetor   = $vetLocal[3];
        $codLocal   = $vetLocal[4];
        $anoExLocal = $vetLocal[5];

        if ($numPlaca == "") {
            $numPlaca = 'NULL';
        } else {
            $numPlaca = "'".$numPlaca."'";
        }

        $valorBem         = str_replace(".","",$valorBem);
        $valorBem         = str_replace(",",".",$valorBem);
        $valorDepreciacao = str_replace(".","",$valorDepreciacao);
        $valorDepreciacao = str_replace(",",".",$valorDepreciacao);
        $dtAquisicao      = dataToSql($dtAquisicao);

        if ($dtGarantia) {
            $dtGarantia      = dataToSql($dtGarantia);
            $campoDtGarantia = ", dt_garantia";
            $valDtGarantia   = ", '$dtGarantia' ";
        }

        if ($dtDepreciacao) {
            $dtDepreciacao      = dataToSql($dtDepreciacao);
            $campoDtDepreciacao = ", dt_depreciacao";
            $valDtDepreciacao   = ", '$dtDepreciacao' ";
        }

        for ($x = 0; $x < $iLoteQtde; $x++) { //FOR para inclusão em lote, valor padrão 1
            //Inclui o novo bem

            $sql .= "INSERT INTO patrimonio.bem
                    (
                        cod_bem, cod_natureza, cod_grupo, cod_especie, numcgm, descricao,
                        detalhamento, dt_aquisicao $campoDtGarantia $campoDtDepreciacao, vl_bem, vl_depreciacao,  identificacao,
                        num_placa
                    ) VALUES (
                        '".$codBem."', '".$natureza."', '".$grupo."', '".$especie."',
                        '".$fornecedor."', '".$descricao."',  '".$detalhamento."','".$dtAquisicao."'
                        $valDtGarantia $valDtDepreciacao, '".$valorBem."', '".$valorDepreciacao."', '".$identificacao."',
                        ". $numPlaca . "
                    );";

            //Inclui histórico para o novo bem
            $sql .= "INSERT INTO patrimonio.historico_bem
                    (
                        cod_bem, cod_situacao, cod_local, cod_setor, cod_departamento,
                        cod_unidade, cod_orgao, ano_exercicio , descricao, timestamp
                    ) VALUES (
                        '".$codBem."', '".$situacao."', '".$codLocal."', '".$codSetor."',
                        '".$codDpto."', '".$codUnidade."', '".$codOrgao."', '".$anoExLocal."' , '".$descSituacao."', now()
                    );";
            //Inclui os atributos
            if ( is_array($atributos) ) {
                foreach ($atributos as $codAtributo => $valorAtributo) {
                    $sql .= "INSERT INTO patrimonio.bem_atributo_especie
                            (
                                cod_bem, cod_atributo, cod_especie,
                                cod_grupo, cod_natureza, valor_atributo
                            ) VALUES (
                                '".$codBem."', '".$codAtributo."', '".$especie."',
                                '".$grupo."', '".$natureza."', '".$valorAtributo."'
                            );";
                }
            }

            //Se for bem comprado insere os dados na tabela bem_comprado
            $codEmpenhoSql = ($codEmpenho != "" || $codEmpenho > 0) ? $codEmpenho : " NULL ";
                $sql .= "INSERT INTO patrimonio.bem_comprado
                        (
                            cod_bem, cod_empenho, exercicio,cod_entidade,nota_fiscal
                        ) VALUES (
                            '".$codBem."', ".$codEmpenhoSql.", '".$exercicioEmpenho."', '".$codEntidade."', '".$numNotaFiscal."'
                        );";
            $codBem = $codBem + 1;
        } //fecha o FOR de inclusão em lote
//        echo $sql;
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();

        if ( $conn->executaSql($sql) ) {
            $ok = true;
        } else {
            $ok = false;
        }
        $conn->fechaBD();

        return $ok;
    }

/**************************************************************************
 Altera os dados de um bem existente
/**************************************************************************/
    public function alterarBem($codBem, $descricao, $detalhamento, $dtAquisicao, $dtDepreciacao, $dtGarantia,
    $valorBem, $valorDepreciacao, $identificacao, $situacao, $descSituacao, $local, $anoExLocal, $fornecedor,
    $atributos, $natureza, $grupo, $especie, $codEmpenho, $numNotaFiscal, $exercicioEmpenho, $numPlaca, $codEntidade) {
        //Pega os dados do local

        //Pega os dados do local
        $vetLocal = preg_split( "/[^a-zA-Z0-9]/", $local );
            $codOrgao = $vetLocal[0];
            $codUnidade = $vetLocal[1];
            $codDpto = $vetLocal[2];
            $codSetor = $vetLocal[3];
            $codLocal = $vetLocal[4];
            $anoExLocal = $vetLocal[5];

        if ($numPlaca == "") {
            $numPlaca = 'NULL';
        } else {
            $numPlaca = "'".$numPlaca."'";
        }

        if ($codEntidade == "") {
            $codEntidade = 0;
        }
        $valorBem         = str_replace(".","",$valorBem);
        $valorBem         = str_replace(",",".",$valorBem);
        $valorDepreciacao = str_replace(".","",$valorDepreciacao);
        $valorDepreciacao = str_replace(",",".",$valorDepreciacao);
        $dtAquisicao = dataToSql($dtAquisicao);
        if ($dtDepreciacao) {
            $dtDepreciacao      = dataToSql($dtDepreciacao);
            $campoDtDepreciacao = "dt_depreciacao = '$dtDepreciacao' ,";
        }
        if ($dtGarantia) {
            $dtGarantia      = dataToSql($dtGarantia);
            $campoDtGarantia = "dt_garantia = '$dtGarantia' ,";
        }

        //Atualiza o registro do bem
        $sql = "UPDATE
                    patrimonio.bem
                SET
                    numcgm = '".$fornecedor."',
                    cod_natureza = '".$natureza."',
                    cod_grupo = '".$grupo."',
                    cod_especie = '".$especie."',
                    descricao = '".$descricao."',
                    detalhamento = '".$detalhamento."',
                    dt_aquisicao = '".$dtAquisicao."',
                    $campoDtDepreciacao
                    $campoDtGarantia
                    vl_bem = '".$valorBem."',
                    vl_depreciacao = '".$valorDepreciacao."',
                    identificacao = '".$identificacao."',
                    num_placa     = ".$numPlaca."
                WHERE
                    cod_bem = '".$codBem."'; ";

        //Adiciona um novo histórico
        $sql .= "INSERT INTO patrimonio.historico_bem (
                    cod_bem, cod_situacao, cod_local, cod_setor, cod_departamento,
                    cod_unidade, cod_orgao, ano_exercicio , descricao, timestamp
                ) VALUES (
                    '".$codBem."', '".$situacao."', '".$codLocal."', '".$codSetor."',
                    '".$codDpto."', '".$codUnidade."', '".$codOrgao."', '".$anoExLocal."' , '".$descSituacao."', now()
                );";

        //Exclui os atributos. Eles serão incluidos novamente logo abaixo (se houver)
        $sql .= "DELETE FROM patrimonio.bem_atributo_especie WHERE cod_bem = '".$codBem."'; ";

        //Inclui os atributos
        if (is_array($atributos)) {
            foreach ($atributos as $codAtributo=>$valorAtributo) {
                $sql .= "INSERT INTO patrimonio.bem_atributo_especie (
                            cod_bem, cod_atributo, cod_especie,
                            cod_grupo, cod_natureza, valor_atributo
                        ) VALUES (
                            '".$codBem."', '".$codAtributo."', '".$especie."',
                            '".$grupo."', '".$natureza."', '".$valorAtributo."'
                        );";
            }
        }

        //Atualiza ou inclui os dados de compra
            //Verifica se existe os dados do bem na tabela de bens comprados

            $codEmpenhoSql = ($codEmpenho != "" || $codEmpenho > 0) ? $codEmpenho : " NULL ";
            if (pegaDado("cod_bem","patrimonio.bem_comprado","Where cod_bem = '".$codBem."'")) {
                //Se existe atualiza
                $sql.=" UPDATE patrimonio.bem_comprado SET              ";
                $sql.="        cod_empenho  = ".$codEmpenhoSql."     ,  ";
                $sql.="        cod_entidade = ".$codEntidade."       ,  ";
                $sql.="        exercicio    = '".$exercicioEmpenho."',  ";
                $sql.="        nota_fiscal  = '".$numNotaFiscal."'      ";
                $sql.="  WHERE cod_bem      = '".$codBem."';            ";

            } else { //Se não existe inclui
                $sql .= "INSERT INTO patrimonio.bem_comprado (
                            cod_bem, cod_empenho, exercicio,cod_entidade
                        ) VALUES (
                            ".$codBem.", ".$codEmpenhoSql.", '".$exercicioEmpenho."', ".$codEntidade."
                        ); ";

            }
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function alterarBem

/**************************************************************************
 Exclui um bem do banco de dados se não houver dados relacionados ao mesmo
/**************************************************************************/
    public function excluirBem($codBem)
    {
        $sql = "Delete From patrimonio.bem_atributo_especie Where cod_bem = '".$codBem."'; ";
        $sql .= "Delete From patrimonio.bem_comprado Where cod_bem = '".$codBem."'; ";
        $sql .= "Delete From patrimonio.historico_bem Where cod_bem = '".$codBem."'; ";
        $sql .= "Delete From patrimonio.bem_atributo_especie Where cod_bem = '".$codBem."'; ";
        $sql .= "Delete From patrimonio.bem Where cod_bem = '".$codBem."'; ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function excluirBem

/**************************************************************************
 Transfere bens de local
/**************************************************************************/
    public function transferirBens($codBem,$local,$situacao,$descSituacao)
    {
        //Pega os dados do local
        $vetLocal = preg_split( "/[^a-zA-Z0-9]/", $local);
            $codOrgao = $vetLocal[0];
            $codUnidade = $vetLocal[1];
            $codDpto = $vetLocal[2];
            $codSetor = $vetLocal[3];
            $codLocal = $vetLocal[4];
            $anoExLocal = $vetLocal[5];

        //Insere um novo histórico para o novo local
        $sql = "";

        // BENS EM LOTE
        if (is_array($codBem)) {

            foreach ($codBem as $bem) {
                $vSituacao = $situacao[$bem];
                $sql .= "Insert Into patrimonio.historico_bem (
                        cod_bem, cod_situacao, cod_local, cod_setor, cod_departamento,
                        cod_unidade, cod_orgao, ano_exercicio , descricao,timestamp
                        ) Values(
                        '".$bem."', '".$vSituacao."', '".$codLocal."', '".$codSetor."',
                        '".$codDpto."', '".$codUnidade."', '".$codOrgao."', '".$anoExLocal."' , '".$descSituacao."', now()
                        );";
            }

        // PATRIMONIO BEM INDIVIDUAL
        } else {

            $sql = "Insert Into patrimonio.historico_bem (
                    cod_bem, cod_situacao, cod_local, cod_setor, cod_departamento,
                    cod_unidade, cod_orgao, ano_exercicio , descricao, timestamp
                    ) Values(
                    '".$codBem."', '".$situacao."', '".$codLocal."', '".$codSetor."',
                    '".$codDpto."', '".$codUnidade."', '".$codOrgao."', '".$anoExLocal."' , '".$descSituacao."', now()
                    ); ";
        }

        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();

        if ($conn->executaSql($sql)) {
            $ok = true;
        } else {
            $ok = false;
        }

        $conn->fechaBD();

        return $ok;

    }//Fim da function transferirBens

/**************************************************************************
 Inclui um bem na tabela bens baixados
/**************************************************************************/
    public function baixarBem($codBem,$dataBaixa,$motivo)
    {
        $dataBaixa = dataToSql($dataBaixa);
        $sql = "Insert Into patrimonio.bem_baixado (
                cod_bem, dt_baixa, motivo
                ) Values (
                '".$codBem."', '".$dataBaixa."', '".$motivo."'
                ) ";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function baixarBem

/**************************************************************************
 Baixa uma faixa de bens em lote
/**************************************************************************/
    public function baixarBemLote($codInicial,$codFinal,$dataBaixa,$motivo)
    {
        $dataBaixa = dataToSql($dataBaixa);
        $sql = "";
        for ($i=$codInicial; $i<=$codFinal; $i++) {
            $sql .= "Insert Into patrimonio.bem_baixado (
                    cod_bem, dt_baixa, motivo
                    ) Values (
                    '".$i."', '".$dataBaixa."', '".$motivo."'
                    ); ";
        }
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function baixarBem

/**************************************************************************
 Entra com o código do bem e retorna todos os dados relativos a ele
 em forma de vetor
/**************************************************************************/
    public function pegaDados($codBem)
    {
        //Carrega os dados principais do bem
        $sql = "Select
                    cod_natureza, cod_grupo, cod_especie,
                    numcgm, descricao, detalhamento, dt_aquisicao, dt_depreciacao,
                    dt_garantia, vl_bem, vl_depreciacao, identificacao, num_placa
                From
                    patrimonio.bem
                Where
                    cod_bem = '".$codBem."'";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->fechaBD();
        $dataBase->vaiPrimeiro();

        if (!$dataBase->eof()) {

            $fornecedor = $dataBase->pegaCampo("numcgm");
            $codGrupoCombo = $dataBase->pegaCampo("cod_natureza")."-".$dataBase->pegaCampo("cod_grupo");

            $vetBens = array(
                codBem=>$codBem,
                codNatureza=>$dataBase->pegaCampo("cod_natureza"),
                codTxtNatureza=>$dataBase->pegaCampo("cod_natureza"),
                codGrupo=>$dataBase->pegaCampo("cod_grupo"),
                codEspecie=>$dataBase->pegaCampo("cod_especie"),
                codTxtEspecie=>$dataBase->pegaCampo("cod_especie"),
                fornecedor=>$fornecedor,
                sfornecedor=>pegaDado("nom_cgm","sw_cgm","Where numcgm = '".$fornecedor."'"),
                descricao=>$dataBase->pegaCampo("descricao"),
                detalhamento=>$dataBase->pegaCampo("detalhamento"),
                dataAquisicao=>$dataBase->pegaCampo("dt_aquisicao"),
                dataDepreciacao=>$dataBase->pegaCampo("dt_depreciacao"),
                dataGarantia=>$dataBase->pegaCampo("dt_garantia"),
                valorBem=>$dataBase->pegaCampo("vl_bem"),
                valorDepreciacao=>$dataBase->pegaCampo("vl_depreciacao"),
                identificacao=>$dataBase->pegaCampo("identificacao"),
                numPlaca=>$dataBase->pegaCampo("num_placa")
            );
        }

        $dataBase->limpaSelecao();

        //Verifica o local e a situação do bem
        $sql = "Select
                    cod_situacao, cod_local, cod_setor, cod_departamento,
                    cod_unidade, cod_orgao, ano_exercicio, descricao
                From
                    patrimonio.historico_bem
                Where
                    cod_bem = '".$codBem."'
                Order
                    by timestamp DESC ";
        //echo $sql;
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->fechaBD();
        $dataBase->vaiPrimeiro();
        if (!$dataBase->eof()) {
            $codSituacao = $dataBase->pegaCampo("cod_situacao");
            $exercicio = $dataBase->pegaCampo("ano_exercicio");
            $codOrgao = $dataBase->pegaCampo("cod_orgao");
            $codDpto = $dataBase->pegaCampo("cod_departamento");
            $codUnidade = $dataBase->pegaCampo("cod_unidade");
            $codSetor = $dataBase->pegaCampo("cod_setor");
            $codLocal = $dataBase->pegaCampo("cod_local");
            $chaveLocal = $codOrgao.".".$codUnidade.".".$codDpto.".".$codSetor.".".$codLocal."/".$exercicio;

            $vetBens[situacao] = $codSituacao;
            $vetBens[codMasSetor] = $codOrgao.".".$codUnidade.".".$codDpto.".".$codSetor.".".$codLocal."/".$exercicio;
            $vetBens[exercicio] = $exercicio;
            $vetBens[descSituacao] = $dataBase->pegaCampo("descricao");
            $vetBens[nomSituacao] = pegaDado("nom_situacao","patrimonio.situacao_bem","Where cod_situacao = '".$codSituacao."'");
            $vetBens[nomLocal] = pegaDado("nom_local","administracao.local","Where cod_local = '".$codLocal."' And cod_setor = '".$codSetor."'  And cod_departamento = '".$codDpto."'  And cod_unidade = '".$codUnidade."'  And cod_orgao = '".$codOrgao."' ");
        }
        $dataBase->limpaSelecao();

        $sql = "Select
                    cod_atributo, valor_atributo
                From
                    patrimonio.bem_atributo_especie
                Where
                    cod_bem = '".$codBem."' ";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->fechaBD();
        $dataBase->vaiPrimeiro();
        if (!$dataBase->eof()) {
            $vetAtributo = Array();
            while (!$dataBase->eof()) {
                $vetAtributo[$dataBase->pegaCampo("cod_atributo")] = $dataBase->pegaCampo("valor_atributo");
                $dataBase->vaiProximo();
            }
            $vetBens[atributos] = $vetAtributo;
        }

        $dataBase->limpaSelecao();

        $sql = "Select
                     cod_empenho, exercicio, cod_entidade,nota_fiscal
                From
                    patrimonio.bem_comprado
                Where
                    cod_bem = '".$codBem."' ";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->fechaBD();
        $dataBase->vaiPrimeiro();

        if (!$dataBase->eof()) {
            $vetBens[codEmpenho]    = $dataBase->pegaCampo("cod_empenho");
            $vetBens[numNotaFiscal] = $dataBase->pegaCampo("nota_fiscal");
            $vetBens[exercicioEmpenho] = $dataBase->pegaCampo("exercicio");
            $vetBens[codEntidade] = $dataBase->pegaCampo("cod_entidade");
        }

        $dataBase->limpaSelecao();

        return $vetBens;
    }//Fim function pegaDados

    /*** Método que lista os bens baixados ***/
        function listaBensBaixados()
        {
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            $select = "select cod_bem, dt_baixa, motivo
                            from patrimonio.bem_baixado order by dt_baixa";
            $dbConfig->abreSelecao($select);
            while (!$dbConfig->eof()) {
                $cod = $dbConfig->pegaCampo("cod_bem");
                $lista[$cod] = $dbConfig->pegaCampo("dt_baixa")."/".$dbConfig->pegaCampo("motivo");
                $dbConfig->vaiProximo();
            }
            $dbConfig->limpaSelecao();
            $dbConfig->fechaBd();

            return $lista;
        }

    /*** Método que exclui a baixa do bem ***/
        function excluiBaixa($codBem)
        {
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            $delete = "delete from patrimonio.bem_baixado where cod_bem = $codBem";
            $result = $dbConfig->executaSql($delete);
            $dbConfig->fechaBd();
            if ($result) {
                return true;
            } else {
                return false;
            }
        }

        function listaEntidades($exercicio)
        {
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            $stSql .= " SELECT                                   \n";
            $stSql .= "     E.cod_entidade,                      \n";
            $stSql .= "     C.nom_cgm                            \n";
            $stSql .= " FROM                                     \n";
            $stSql .= "     orcamento.entidade      as   E,      \n";
            $stSql .= "     sw_cgm                  as   C       \n";
            $stSql .= " WHERE                                    \n";
            $stSql .= "     E.numcgm = C.numcgm                  \n";
            $stSql .= " AND  E.exercicio = '".$exercicio."'       \n";

            $dbConfig->abreSelecao($stSql);
            $count = 0;
            while (!$dbConfig->eof()) {
                $lista[$count]["cod_entidade"] = $dbConfig->pegaCampo("cod_entidade");
                $lista[$count]["nom_entidade"] = $dbConfig->pegaCampo("nom_cgm");
                $count++;
                $dbConfig->vaiProximo();
            }
            $dbConfig->limpaSelecao();
            $dbConfig->fechaBd();

            return $lista;
        }
}
?>
