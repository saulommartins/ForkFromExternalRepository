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
 *
 * Data de Criação: 27/10/2005

 * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
 * @author Documentor: Cassiano de Vasconcellos Ferreira

 * @package framework
 * @subpackage componentes

 Casos de uso: uc-01.01.00

 $Id: processosLegado.class.php 62581 2015-05-21 14:05:03Z michel $

 */

include_once 'usuarioLegado.class.php'; // Esta classe é utilizada para buscar o anoExercicioSetor do usuario

class processosLegado
{
    public $codProcesso;
    public $codAndamento;
    public $anoExercicio;
    public $codUsuario;
    public $descricao;
    public $setorPadrao;
    public $proxAndamentoPadrao;
    public $proxAndamentoPadraoExercicio;

    /** Método Construtor **/
    public function processosLegado()
    {
        $this->codAndamento = "";
        $this->codProcesso = "";
        $this->anoExercicio = "";
        $this->codUsuario = "";
        $this->descricao = "";
        $this->setorPadrao = "";
        $this->proxAndamentoPadrao = "";
        $this->proxAndamentoPadraoExercicio = "";
    }

/**************************************************************************
 Entra com o código do processo e retorna todos os dados relevantes
 em forma de vetor
/**************************************************************************/
    public function pegaDados($codProcesso,$anoExercicio)
    {
        //Pega os dados principais do processo
        $sql = "SELECT
                        A.cod_processo
                     ,  A.ano_exercicio
                     ,  A.cod_andamento
                     ,  T.nom_assunto
                     ,  P.cod_situacao
                     ,  P.cod_classificacao
                     ,  P.cod_assunto
                -- Coluna retirada da tabela sw_processo
                --	 ,  P.numcgm
                     ,  P.cod_usuario
                     ,  P.observacoes
                     ,  P.resumo_assunto
                     ,  P.timestamp
                     ,  S.nom_situacao
                     ,  C.nom_classificacao

                  FROM  sw_andamento as A
                     ,  sw_ultimo_andamento as U
                     ,  sw_processo as P
                     ,  sw_assunto as T
                     ,  sw_situacao_processo as S
                     ,  sw_classificacao as C

                 WHERE  A.cod_andamento		= U.cod_andamento
                   AND  A.cod_processo		= U.cod_processo
                   AND  A.ano_exercicio		= U.ano_exercicio
                   AND  A.cod_processo		= P.cod_processo
                   AND  A.ano_exercicio		= P.ano_exercicio
                   AND  P.cod_classificacao 	= T.cod_classificacao
                   AND  P.cod_assunto 		= T.cod_assunto
                   AND  C.cod_classificacao 	= T.cod_classificacao
                   AND  P.cod_situacao		= S.cod_situacao
                   AND  P.cod_processo		= '".$codProcesso."'
                   AND  P.ano_exercicio 		= '".$anoExercicio."' ";
        //Pega os dados encontrados em uma query
        $conn = new dataBaseLegado ;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
            if (!$conn->eof()) {
                $codAndamento = $conn->pegaCampo("cod_andamento");
                //Inicia o vetor com os dados do processo
                $vet['codProcesso']   = $codProcesso;
                $vet['anoExercicio']  = $anoExercicio;
                $vet['codClassif']    = $conn->pegaCampo("cod_classificacao");
                $vet['codAssunto']    = $conn->pegaCampo("cod_assunto");
                $vet['classificacao'] = $conn->pegaCampo("nom_classificacao");
                $vet['assunto']       = $conn->pegaCampo("nom_assunto");
                $vet['codAndamento']  = $codAndamento;
                $vet['codSituacao']   = $conn->pegaCampo("cod_situacao");
                $vet['nomSituacao']   = $conn->pegaCampo("nom_situacao");
                #$vet['numCgm']        = $conn->pegaCampo("numcgm");
                $vet['codUsuario']    = $conn->pegaCampo("cod_usuario");
                $vet['observacoes']   = $conn->pegaCampo("observacoes");
                $vet['resumo']        = $conn->pegaCampo("resumo_assunto");
                $vet['timestamp']     = $conn->pegaCampo("timestamp");
            }
        $conn->limpaSelecao();
        $teste_erro=0;
        //Verificar se o processo é do tipo matrícula
        if ($numMatricula = SistemaLegado::pegaDado("num_matricula","sw_processo_matricula","Where cod_processo = '".$codProcesso."' and ano_exercicio = '".$anoExercicio."' ")) {
            $vet[numMatricula] = $numMatricula;
            $vet[vinculo] = "imobiliaria";
        }
        //Verificar se o processo é do tipo inscrição
        if ($numInscricao = SistemaLegado::pegaDado("num_inscricao","sw_processo_inscricao","Where cod_processo = '".$codProcesso."' and ano_exercicio = '".$anoExercicio."' ")) {
            $vet[numInscricao] = $numInscricao;
            $vet[vinculo] = "inscricao";
        }
        //Verificar o setor em que o processo encontra-se. De acordo com o último andamento
        $sql = "SELECT
                      cod_orgao,
                      -- cod_unidade,
                      -- cod_departamento,
                      -- cod_setor,
                      -- ano_exercicio_setor,
                      cod_usuario
                FROM  sw_andamento
               WHERE  cod_andamento = '".$codAndamento."'
                 AND  cod_processo = '".$codProcesso."'
                 AND  ano_exercicio = '".$anoExercicio."' ";
        //Pega os dados encontrados em uma query
        $conn = new dataBaseLegado ;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
            if (!$conn->eof()) {
                $codOrgao = $conn->pegaCampo("cod_orgao");
                //Grava no vetor
                $vet['codOrgao'] = $codOrgao;
            }
        $conn->limpaSelecao();

        //Verificar os documentos entregues
        $sql = "Select DP.cod_documento, D.nom_documento
                From sw_documento_processo as DP, sw_documento as D
                Where DP.cod_documento = D.cod_documento
                And cod_processo = '".$codProcesso."'
                And exercicio = '".$anoExercicio."' ";
        //Pega os dados encontrados em uma query
        $conn = new dataBaseLegado ;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();

    $docs = array();
        while (!$conn->eof()) {
            $codDocumento = $conn->pegaCampo("cod_documento");
            $docs["doc".$codDocumento] = $conn->pegaCampo("nom_documento");
        $conn->vaiProximo();
        }
        $conn->limpaSelecao();

        $vet['documentos'] = $docs;
    Sessao::write('documentos_processos',$docs);

        /* Verifica se é processo Arquivado Temporário ou Arquivado Definitivo
         * Se for então busca dados do arquivamento
         */
        if ($vet['codSituacao'] == '5' || $vet['codSituacao'] == '9') {
            //Verificar os documentos entregues
            $sql = "SELECT
                        prar.timestamp_arquivamento,
                        TO_DATE(TO_CHAR(prar.timestamp_arquivamento, 'dd/mm/yyyy'), 'dd/mm/yyyy') AS data_arquivamento,
                        prar.texto_complementar
                    FROM
                        sw_processo pr INNER JOIN
                        sw_processo_arquivado prar ON
                        (pr.ano_exercicio = prar.ano_exercicio AND
                        pr.cod_processo = prar.cod_processo)
                    WHERE
                        pr.cod_processo = '".$codProcesso."' AND
                        pr.ano_exercicio = '".$anoExercicio."' ";

            $conn = new dataBaseLegado ;
            $conn->abreBD();
            $conn->abreSelecao($sql);
            $conn->fechaBD();
            $conn->vaiPrimeiro();
            while (!$conn->eof()) {
                $stTextoComplementar = urldecode($conn->pegaCampo("texto_complementar"));
                $dtDataArquivamento = $conn->pegaCampo("data_arquivamento");
                $conn->vaiProximo();
            }
            $vet[textoComplementar]	= $stTextoComplementar;
            $vet[dataArquivamento] = $dtDataArquivamento;
            $conn->limpaSelecao();
        }

        return $vet;
    }//Fim da function pegaDados

/**************************************************************************
 Entra com o código do processo e o exercício e retorna uma matriz com
 todos os andamentos do proceesso
***************************************************************************/
    public function pegaDadosAndamento($codProcesso,$anoExercicio)
    {
        $sql = "
              SELECT
                      sw_andamento.cod_andamento
                   ,  sw_andamento.cod_processo
                   ,  sw_andamento.cod_orgao
                   ,  sw_andamento.cod_usuario
                   ,  sw_andamento.ano_exercicio
                   ,  sw_andamento.timestamp
                   ,  ( SELECT cod_organograma FROM organograma.orgao_nivel WHERE orgao_nivel.cod_orgao = sw_andamento.cod_orgao LIMIT 1) as cod_organograma
                   ,  (
                        SELECT  organograma.fn_consulta_orgao(cod_organograma, sw_andamento.cod_orgao)
                          FROM  organograma.orgao_nivel
                         WHERE  orgao_nivel.cod_orgao = sw_andamento.cod_orgao
                         LIMIT 1
                       ) AS orgao_reduzido
                   ,  recuperadescricaoorgao(sw_andamento.cod_orgao, sw_andamento.timestamp::date) as descricao

                FROM  sw_andamento

               WHERE  sw_andamento.cod_processo  = '".$codProcesso."'
                 AND  sw_andamento.ano_exercicio = '".$anoExercicio."'

            ORDER BY  sw_andamento.cod_andamento";

        $conn = new dataBaseLegado ;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
        $i = 0;

        while (!$conn->eof()) {
            $codAndamento               = $conn->pegaCampo("cod_andamento");
            $codOrgao                   = $conn->pegaCampo("cod_orgao");
            $nomOrgao                   = $conn->pegaCampo("descricao");
            $chaveOrgao                 = $conn->pegaCampo("orgao_reduzido");
            $anoExercicio               = $conn->pegaCampo("ano_exercicio");

            $vet[$i]['codProcesso']       = $conn->pegaCampo("cod_processo");
            $vet[$i]['codAndamento']      = $codAndamento;
            $vet[$i]['codOrgao']          = $codOrgao;
            $vet[$i]['nomOrgao']          = $nomOrgao;
            $vet[$i]['anoExercicio']      = $anoExercicio;
            $vet[$i]['chaveOrgao']        = $chaveOrgao;
            $vet[$i]['codUsuario']        = $conn->pegaCampo("cod_usuario");
            $vet[$i]['username']          = pegaDado("username","administracao.usuario","Where numcgm = '".$vet[$i]['codUsuario']."'");
            $vet[$i]['timestamp']         = $conn->pegaCampo("timestamp");
            $vet[$i]['nomSetor']          = $nomOrgao;

            $vet[$i]['coduser_encaminhamento'] = "";
            $vet[$i]['coduser_recebimento']    = "";
            $vet[$i]['user_encaminhamento']    = "";
            $vet[$i]['user_recebimento']       = "";
            $vet[$i]['dt_encaminhamento']      = "";
            $vet[$i]['dt_recebimento']         = "";

            $i++;
            $conn->vaiProximo();
        }

        $conn->limpaSelecao();

        return $vet;
    }

    public function pegaDadosAndamento2($codProcesso,$anoExercicio)
    {
        $sql = "SELECT
                        AI.cod_andamento,
                        AI.ano_exercicio,
                        AI.cod_usuario,
                        AI.cod_orgao,
                        AI.cod_unidade,
                        AI.cod_departamento,
                        AI.cod_setor,
                        AI.nom_setor,
                        AI.ano_exercicio_setor,
                        AI.recebimento,
                        AI.cod_processo,
                        AF.encaminhamento,
                        AI.descricao,
                        AI.username,
                        AI.userRecebe
                FROM
                    (SELECT
                        DISTINCT
                            A.cod_andamento,
                            A.ano_exercicio,
                            A.cod_usuario,
                            A.cod_orgao,
                            A.cod_unidade,
                            A.cod_departamento,
                            A.cod_setor,
                            S.nom_setor,
                            A.ano_exercicio_setor,
                            C.timestamp AS recebimento,
                            A.cod_processo,
                            D.descricao,
                            U.username,
                            UR.username AS userRecebe
                    FROM
                        sw_andamento          AS A,
                        sw_recebimento        AS C,
                        sw_despacho           AS D,
                        administracao.setor              AS S,
                        administracao.usuario            AS U,
                        administracao.usuario            AS UR,
                        sw_assinatura_digital AS AD
                    WHERE
                        A.cod_processo     = '".$codProcesso."'  AND
                        A.ano_exercicio    = '".$anoExercicio."' AND
                        C.cod_processo     = A.cod_processo       AND
                        C.ano_exercicio    = A.ano_exercicio      AND
                        C.cod_andamento    = A.cod_andamento      AND
                        D.cod_andamento    = A.cod_andamento      AND
                        D.cod_processo     = A.cod_processo       AND
                        D.ano_exercicio    = A.ano_exercicio      AND
                        S.cod_orgao        = A.cod_orgao          AND
                        S.cod_unidade      = A.cod_unidade        AND
                        S.cod_departamento = A.cod_departamento   AND
                        S.cod_setor        = A.cod_setor          AND
                        U.numcgm           = A.cod_usuario        AND
                        UR.numcgm          = AD.cod_usuario       AND
                        AD.cod_processo    = C.cod_processo
                    ORDER BY
                        A.cod_andamento) AS AI
                LEFT JOIN
                    (SELECT
                        A.cod_andamento,
                        A.timestamp      AS encaminhamento
                    FROM
                        sw_andamento    AS A
                    WHERE
                        A.cod_processo = '".$codProcesso."'   AND
                        A.ano_exercicio = '".$anoExercicio."'
                    ORDER BY
                        A.cod_andamento) AS AF
                ON
                    AI.cod_andamento = (AF.cod_andamento - 1)";

        //Pega os dados encontrados em uma query
        $conn = new dataBaseLegado ;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
            $i = 0;
            while (!$conn->eof()) {
                $codOrgao                   = $conn->pegaCampo("cod_orgao");
                $codUnidade                 = $conn->pegaCampo("cod_unidade");
                $codDpto                    = $conn->pegaCampo("cod_departamento");
                $codSetor                   = $conn->pegaCampo("cod_setor");
                $anoExSetor                 = $conn->pegaCampo("ano_exercicio_setor");
                //Grava no vetor
                $vet[$i][codOrgao]          = $codOrgao;
                $vet[$i][codUnidade]        = $codUnidade;
                $vet[$i][codDpto]           = $codDpto;
                $vet[$i][codSetor]          = $codSetor;
                $vet[$i][anoExercicioSetor] = $anoExSetor;
                $vet[$i][chaveSetor]        = $codOrgao.".".$codUnidade.".".$codDpto.".".$codSetor;
                $vet[$i][nomSetor]          = pegaDado("nom_setor","administracao.setor","Where cod_setor = '".$codSetor."' And cod_departamento = '".$codDpto."' And cod_unidade = '".$codUnidade."' And cod_orgao = '".$codOrgao."' And ano_exercicio = '".$anoExSetor."' ");
                $vet[$i][codAndamento]      = $conn->pegaCampo("cod_andamento");
                $vet[$i][codUsuario]        = $conn->pegaCampo("cod_usuario");
                $vet[$i][timestamp]         = $conn->pegaCampo("encaminhamento");
                $vet[$i][timestamp2]        = $conn->pegaCampo("recebimento");
                $vet[$i][codProcesso]       = $conn->pegaCampo("cod_processo");
                $vet[$i][username]          = $conn->pegaCampo("username");
                $i++;
                $conn->vaiProximo();
            }
        $conn->limpaSelecao();

        return $vet;
    }//Fim da function pegaDadosAndamento

/**************************************************************************
 Entra com o código do processo, o exercício e o código de andamento
 e retorna uma matriz com todos os despachos para o andamento
***************************************************************************/
    public function pegaDadosDespacho($codProcesso,$anoExercicio,$codAndamento)
    {
        $sql = 	"SELECT
                    cod_usuario,
                    descricao,
                    timestamp
                FROM
                    sw_despacho
                WHERE
                    cod_andamento = '".$codAndamento."' AND
                    cod_processo  = '".$codProcesso."'  AND
                    ano_exercicio = '".$anoExercicio."'
                ORDER BY
                    timestamp";
        //echo $sql."<br>";
        //Pega os dados encontrados em uma query
        $conn = new dataBaseLegado ;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
            if($conn->numeroDeLinhas==0)
                $vet = false;
            $i = 0;
            while (!$conn->eof()) {
                $codUsuario = $conn->pegaCampo("cod_usuario");
                $vet[$i][codUsuario] = $codUsuario;
                $vet[$i][nomUsuario] = pegaDado("username","administracao.usuario","Where numcgm = '".$codUsuario."'");
                $vet[$i][descricao] = $conn->pegaCampo("descricao");
                $vet[$i][timestamp] = $conn->pegaCampo("timestamp");
                $i++;
                $conn->vaiProximo();
            }
        $conn->limpaSelecao();

        return $vet;
    }

/**************************************************************************
 Método para atribuir um processo a um setor
/**************************************************************************/
    public function incluiProcesso(  $codProcesso         
                                    ,$especieProcesso
                                    ,$codClassificacao    
                                    ,$codAssunto
                                    ,$numCgm              
                                    ,$numMatricula
                                    ,$numInscricao        
                                    ,$observacoes
                                    ,$resumo              
                                    ,$refAnterior
                                    ,$processosAnexos     
                                    ,$codUsuario
                                    ,$codOrgao
                                    ,$codUnidade
                                    ,$codDpto
                                    ,$codSetor
                                    ,$anoExercicio
                                    ,$anoExercicioSetor
                                    ,$codDocumentos
                                    ,$conf
                                    ,$valorAtributo
                                    ,$codMasSetor
                                    ,$interessados
                                    ,$permitidos
                                    ,$centroCusto)
    {
        //Include da classes de mapeamento
        include_once CAM_GA_PROT_MAPEAMENTO."TProcesso.class.php";
        include_once CAM_GA_PROT_MAPEAMENTO."TProtocoloAndamento.class.php";
        include_once CAM_GA_PROT_MAPEAMENTO."TProtocoloCopiaDigital.class.php";
        include_once CAM_GA_PROT_MAPEAMENTO."TProtocoloDocumentoProcesso.class.php";
        include_once CAM_GA_PROT_MAPEAMENTO."TProtocoloProcessoInteressado.class.php";
        include_once CAM_GA_PROT_MAPEAMENTO."TProtocoloProcessoConfidencial.class.php";

        $obErro                           = new Erro();
        $obTransacao                      = new Transacao();
        $obTProcesso                      = new TProcesso();
        $obTProtocoloAndamento            = new TProtocoloAndamento();
        $obTProtocoloCopiaDigital         = new TProtocoloCopiaDigital();
        $obTProtocoloDocumentoProcesso    = new TProtocoloDocumentoProcesso();
        $obTProtocoloProcessoInteressado  = new TProtocoloProcessoInteressado();
        $obTProtocoloProcessoConfidencial = new TProtocoloProcessoConfidencial();

        // numeracao de processo em manual
        // deve retornar mensagem caso ja exista o processo.
        $tipoNumeracao = pegaConfiguracao("tipo_numeracao_processo",5);        
        if ($tipoNumeracao == 2) {
            $inCodProcesso = pegaDado("cod_processo","sw_processo","WHERE cod_processo = ".$codProcesso." AND ano_exercicio = '".$anoExercicio."'");
            if (is_null($inCodProcesso)) {
                $ok = false;
                return $ok;
            }
        }

        $obTProcesso_ = new TProcesso();
        $obTProcesso_->recuperaTodos($rsProcesso_, " WHERE cod_processo = '".$codProcesso."' and ano_exercicio = '".$anoExercicio."' ");
        if($rsProcesso_->getNumLinhas() > 0 ) {
            $obErro->setDescricao('O processo número '.$codProcesso.'/'.$anoExercicio.' já existe na base de dados. O sistema não consegue inserir um mesmo número de processo/ano se já houver um cadastrado.');
        }
        
        if ( !$obErro->ocorreu() ) {
            //Constroi a primeira query
            $resumo      = preg_replace("/'/", "''", $resumo);
            $observacoes = preg_replace("/'/", "''", $observacoes);
            $obTProcesso->setDado( "cod_processo"       , $codProcesso      );
            $obTProcesso->setDado( "cod_situacao"       , 2                 );
            $obTProcesso->setDado( "ano_exercicio"      , $anoExercicio     );
            $obTProcesso->setDado( "cod_classificacao"  , $codClassificacao );
            $obTProcesso->setDado( "cod_assunto"        , $codAssunto       );
            $obTProcesso->setDado( "cod_usuario"        , $codUsuario       );
            $obTProcesso->setDado( "observacoes"        , $observacoes      );
            $obTProcesso->setDado( "confidencial"       , $conf             );
            $obTProcesso->setDado( "resumo_assunto"     , $resumo           );
            $obTProcesso->setDado( "cod_centro"         , $centroCusto      );
            $obErro = $obTProcesso->inclusao($obTransacao);
        }
        
        // Inclusão de interessados na nova tabela de sw_processo_interessado
        foreach ($interessados as $chave => $valor) {
            if ( !$obErro->ocorreu() ) {
                $obTProtocoloProcessoInteressado->setDado( "ano_exercicio" , $anoExercicio );
                $obTProtocoloProcessoInteressado->setDado( "cod_processo"  , $codProcesso  );
                $obTProtocoloProcessoInteressado->setDado( "numcgm"        , $valor['numCgm'] );
                $obErro = $obTProtocoloProcessoInteressado->inclusao($obTransacao);
            }
        }

        # Foreach responsável por incluir na tabelas os CGMs que terão permissão para ver o processo
        # caso o mesmo seja confidencial
        if ((bool)$conf == true) {
            if (is_array($permitidos) && count($permitidos) > 0) {
                foreach ($permitidos as $chave => $valor) {
                    if ( !$obErro->ocorreu() ) {
                        $obTProtocoloProcessoConfidencial->setDado( "ano_exercicio" , $anoExercicio );
                        $obTProtocoloProcessoConfidencial->setDado( "cod_processo"  , $codProcesso  );
                        $obTProtocoloProcessoConfidencial->setDado( "numcgm"        , $valor['numCgmAcesso'] );
                        $obErro = $obTProtocoloProcessoConfidencial->inclusao($obTransacao);
                    }
                }
            }
        }

        if ($especieProcesso == 'imobiliaria'){
            if ( !$obErro->ocorreu() ) {
                include_once CAM_GA_PROT_MAPEAMENTO."TProtocoloProcessoMatricula.class.php";
                $obTProtocoloProcessoMatricula = new TProtocoloProcessoMatricula();
                $obTProtocoloProcessoMatricula->setDado( "cod_processo"  , $codProcesso  );
                $obTProtocoloProcessoMatricula->setDado( "ano_exercicio" , $anoExercicio );
                $obTProtocoloProcessoMatricula->setDado( "num_matricula" , $numMatricula );
                $obErro = $obTProtocoloProcessoMatricula->inclusao( $obTransacao );
            }
        } elseif ($especieProcesso == 'inscricao') {
            if ( !$obErro->ocorreu() ) {
                include_once CAM_GA_PROT_MAPEAMENTO."TProtocoloProcessoInscricao.class.php";
                $obTProtocoloProcessoInscricao = new TProtocoloProcessoInscricao();
                $obTProtocoloProcessoInscricao->setDado( "cod_processo"  , $codProcesso  );
                $obTProtocoloProcessoInscricao->setDado( "ano_exercicio" , $anoExercicio );
                $obTProtocoloProcessoInscricao->setDado( "num_inscricao" , $numInscricao );
                $obErro = $obTProtocoloProcessoInscricao->inclusao( $obTransacao );
            }
        }

        //inclusão dos atributos de processo
        if (!empty($valorAtributo)) {
            include_once CAM_GA_PROT_MAPEAMENTO."TProtocoloAssuntoAtributoValor.class.php";
            $obTProtocoloAssuntoAtributoValor = new TProtocoloAssuntoAtributoValor();
            while (list($key, $val) = each($valorAtributo)) {
                if ( !$obErro->ocorreu() ) {
                    $obTProtocoloAssuntoAtributoValor->setDado( "cod_atributo"      , $key              );
                    $obTProtocoloAssuntoAtributoValor->setDado( "cod_assunto"       , $codAssunto       );
                    $obTProtocoloAssuntoAtributoValor->setDado( "cod_classificacao" , $codClassificacao );
                    $obTProtocoloAssuntoAtributoValor->setDado( "cod_processo"      , $codProcesso      );
                    $obTProtocoloAssuntoAtributoValor->setDado( "exercicio"         , $anoExercicio     );
                    $obTProtocoloAssuntoAtributoValor->setDado( "valor"             , $val              );
                    $obErro = $obTProtocoloAssuntoAtributoValor->inclusao( $obTransacao );
                }
            }
        }
        
        $anoExercicio = pegaConfiguracao("ano_exercicio");
        $codDocumentoProcesso = pegaId("cod_copia", "sw_copia_digital", "where exercicio= '".$anoExercicio."' ");
        $exp = explode("=", Sessao::getId());
        $session = $exp[1];
        $diretorio = CAM_PROTOCOLO."tmp/".$session;

        if (!empty($codDocumentos)) {
            while (list($key, $val) = each($codDocumentos)) {
                if (file_exists($diretorio)) {
                    $lista = opendir($diretorio);
                    while ($file = readdir($lista)) {
                        if ($file == $val) {
                            if ($file == '.' || $file == '..' || $file == 'naoExclua.txt' || $file=='CVS') {
                                    continue;
                            }
                            $dirDoc = $diretorio."/".$val;
                            //echo $dirDoc."<br>\n";
                            if (file_exists($dirDoc)) {
                                $dirDoc = $diretorio."/".$val;
                                $listaDoc = opendir($dirDoc);
                                while ($fileDoc = readdir($listaDoc)) {
                                    if ($fileDoc == '.' || $fileDoc == '..') {
                                        continue;
                                    }
                                    
                                    $extensao = explode(".", $fileDoc);
                                    $oldFile = explode("§", $fileDoc);

                                    if ($extensao[1] == "jpg") {
                                        $imagem = "t";
                                    } else {
                                        $imagem = "f";
                                    }
                                    
                                    $dirAnexo = pegaConfiguracao("diretorio")."/anexos/".$codDocumentoProcesso."_".$val."_".$codProcesso."_".$anoExercicio."_".$oldFile[1];
                                    $dirAnexo = CAM_PROTOCOLO."anexos/".$codDocumentoProcesso."_".$val."_".$codProcesso."_".$anoExercicio."_".$oldFile[1];

                                    # Nome do arquivo formatado para ser único
                                    $stNomeArquivo = $codDocumentoProcesso.'_'.$val.'_'.$codProcesso.'_'.$anoExercicio."_".$oldFile[1];

                                    $fileDoc = $dirDoc."/".$fileDoc;
                                    
                                    # Copia o arquivo para o diretório protocolo/tmp
                                    $stDirTmp = CAM_PROTOCOLO."tmp/".$stNomeArquivo;
                                    copy($fileDoc, $stDirTmp);
                                   
                                    # Copia para o diretório anexos, usado para exibir os anexos.
                                    copy($fileDoc, $dirAnexo);
                                    
                                    if ($teste_erro==0) {
                                        if ( !$obErro->ocorreu() ) {
                                            $obTProtocoloDocumentoProcesso->setDado( "cod_documento" , $val          );
                                            $obTProtocoloDocumentoProcesso->setDado( "cod_processo"  , $codProcesso  );
                                            $obTProtocoloDocumentoProcesso->setDado( "exercicio"     , $anoExercicio );
                                            $obErro = $obTProtocoloDocumentoProcesso->inclusao( $obTransacao );
                                        }
                                    }
                                    
                                    $teste_erro=1;
                                    
                                    if ( !$obErro->ocorreu() ) {
                                        $obTProtocoloCopiaDigital->setDado( "cod_copia"     , $codDocumentoProcesso );
                                        $obTProtocoloCopiaDigital->setDado( "cod_documento" , $val                  );
                                        $obTProtocoloCopiaDigital->setDado( "cod_processo"  , $codProcesso          );
                                        $obTProtocoloCopiaDigital->setDado( "exercicio"     , $anoExercicio         );
                                        $obTProtocoloCopiaDigital->setDado( "imagem"        , $imagem               );
                                        $obTProtocoloCopiaDigital->setDado( "anexo"         , $stNomeArquivo        );
                                        $obErro = $obTProtocoloCopiaDigital->inclusao( $obTransacao );
                                    }
                                    $codDocumentoProcesso = $codDocumentoProcesso + 1;
                                    unlink($fileDoc);
                                }
                                $teste_erro=0;
                            }
                        }
                    }
                }
                $nao = $diretorio."/".$val;
                if (!(file_exists($nao))) {
                    if ( !$obErro->ocorreu() ) {
                        $obTProtocoloDocumentoProcesso->setDado( "cod_documento" , $val          );
                        $obTProtocoloDocumentoProcesso->setDado( "cod_processo"  , $codProcesso  );
                        $obTProtocoloDocumentoProcesso->setDado( "exercicio"     , $anoExercicio );
                        $obErro = $obTProtocoloDocumentoProcesso->inclusao( $obTransacao );
                    }
                    $codDocumentoProcesso = $codDocumentoProcesso + 1;
                }
                //echo $dirDoc."<br>";
                if (file_exists($dirDoc)) {
                    rmdir($dirDoc);
                }
            }
        }
        if (file_exists($diretorio)) {
            rmdir($diretorio);
        }

        # INSERE O ANDAMENTO DA INCLUSÃO DO PROCESSO
        if ( !$obErro->ocorreu() ) {
            $obTProtocoloAndamento->setDado( "cod_andamento" , 0                        );
            $obTProtocoloAndamento->setDado( "cod_processo"  , $codProcesso             );
            $obTProtocoloAndamento->setDado( "ano_exercicio" , $anoExercicio            );
            $obTProtocoloAndamento->setDado( "cod_orgao"     , Sessao::read('codOrgao') );
            $obTProtocoloAndamento->setDado( "cod_usuario"   , $codUsuario              );
            $obTProtocoloAndamento->setDado( "cod_situacao"  , 2                        );
            $obErro = $obTProtocoloAndamento->inclusao( $obTransacao );
        }

        # INSERE O ANDAMENTO DO ENCAMINHAMENTO PARA O SETOR DE DESTINO.
        if ( !$obErro->ocorreu() ) {
            $obTProtocoloAndamento->setDado( "cod_andamento" , 1             );
            $obTProtocoloAndamento->setDado( "cod_processo"  , $codProcesso  );
            $obTProtocoloAndamento->setDado( "ano_exercicio" , $anoExercicio );
            $obTProtocoloAndamento->setDado( "cod_orgao"     , $codOrgao     );
            $obTProtocoloAndamento->setDado( "cod_usuario"   , $codUsuario   );
            $obTProtocoloAndamento->setDado( "cod_situacao"  , 2             );
            $obErro = $obTProtocoloAndamento->inclusao( $obTransacao );
        }

        if ( !$obErro->ocorreu() ) {
            $ok = true;
        }else{
            $ok = false;
        }        
        return $ok;
    }

/**************************************************************************
Método para recebimento de varios processos com assinatura digital
/**************************************************************************/
    public function recebeProcessos($codProcesso,$codUsuario)
    {
 $vet = explode("-",$codProcesso);
  $codP = $vet[0];
  $anoEx = $vet[1];
  $codAnd = $vet[2];

$sql = "";
$sql = "select
           cod_processo_pai
         , cod_processo_filho
         , exercicio_pai
         , exercicio_filho
        from
           sw_processo_apensado
        where
           cod_processo_pai = ".$codP." AND
           exercicio_pai = '".$anoEx."'
           and timestamp_desapensamento is null;";
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->vaiPrimeiro();
        $registros = $conn->numeroDeLinhas;
//Rotina para inserir os filhos(apensados)
//echo $registros;
if ($registros > 0) {
    for ($i = 1; $i <= $registros; $i++) {
       $codProcesso_pai   = $conn->pegaCampo("cod_processo_pai");
       $codProcesso_filho = $conn->pegaCampo("cod_processo_filho");
       $exercicio_pai     = $conn->pegaCampo("exercicio_pai");
       $exercicio_filho   = $conn->pegaCampo("exercicio_filho");
       $codAndamento = pegaID("cod_andamento","sw_andamento","Where cod_processo = '$codProcesso_filho' And ano_exercicio = '".$exercicio_filho."'");
$codAndamento = $codAndamento - 1;
//echo $codAndamento;
            //Insere o processo na tabela de recebimento
            $sql .= "Insert Into sw_recebimento (
                    cod_andamento, cod_processo, ano_exercicio
                    ) Values(
                    '".$codAndamento."','".$codProcesso_filho."','".$exercicio_filho."'); ";
            //Insere uma assinatura digital com o código do logado
            $sql .= "Insert Into sw_assinatura_digital (
                    cod_andamento, cod_processo, ano_exercicio, cod_usuario
                    ) Values(
                    '".$codAndamento."','".$codProcesso_filho."','".$exercicio_filho."','".$codUsuario."'); ";

        //Insere um despacho nos filhos(apensados)
        $descricao= "Trâmite em Apenso ao Processo N. ".mascaraProcesso($codProcesso_pai, $exercicio_pai);
        $this->setaValorDespacho($codAndamento,$codProcesso_filho,$anoExercicio,0,$descricao);
        $select =   "SELECT
                        cod_andamento
                    FROM
                        sw_despacho
                    WHERE
                        ano_exercicio = '".$exercicio_filho."'  AND
                        cod_andamento = ".$this->codAndamento." AND
                        cod_processo  = ".$this->codProcesso."  AND
                        cod_usuario   = ".$this->codUsuario;

        $dbSelect = new databaseLegado;
        $dbSelect->abreBd();
        $dbSelect->abreSelecao($select);
        if ($dbSelect->numeroDeLinhas > 0) {
            $sql .=   "DELETE FROM
                            sw_despacho
                        WHERE
                            ano_exercicio = '".$exercicio_filho."'  AND
                            cod_andamento = ".$this->codAndamento." AND
                            cod_processo  = ".$this->codProcesso."  AND
                            cod_usuario   = ".$this->codUsuario.";";
            //echo $sql."<br>";
                $sql .=   "INSERT INTO
                                sw_despacho
                            (
                                cod_andamento,
                                cod_processo,
                                ano_exercicio,
                                cod_usuario,
                                descricao
                            )
                            VALUES
                            (
                                '".$this->codAndamento."',
                                '".$this->codProcesso."',
                                '".$exercicio_filho."',
                                '".$this->codUsuario."',
                                '".$this->descricao."'
                            );";

            } else {
            $sql .=   "INSERT INTO
                            sw_despacho
                        (
                            cod_andamento,
                            cod_processo,
                            ano_exercicio,
                            cod_usuario,
                            descricao
                        )
                        VALUES
                        (
                            '".$this->codAndamento."',
                            '".$this->codProcesso."',
                            '".$exercicio_filho."',
                            '".$this->codUsuario."',
                            '".$this->descricao."'
                        );";
}

$conn->vaiProximo();
}
}
            //Insere o processo na tabela de recebimento
            $sql .= "Insert Into sw_recebimento (
                    cod_andamento, cod_processo, ano_exercicio
                    ) Values(
                    '".$codAnd."','".$codP."','".$anoEx."'); ";
            //Insere uma assinatura digital com o código do logado
            $sql .= "Insert Into sw_assinatura_digital (
                    cod_andamento, cod_processo, ano_exercicio, cod_usuario
                    ) Values(
                    '".$codAnd."','".$codP."','".$anoEx."','".$codUsuario."'); ";
            //Atualiza a situação do processo para 'Em andamento, recebido'
            $sql .= "Update sw_processo
                    Set cod_situacao = '3'
                    Where cod_processo = '".$codP."'
                    And ano_exercicio = '".$anoEx."'; ";
        //Chama a classe do banco de dados e executa a query

        $conn = new dataBaseLegado ;
        $conn->abreBD();
            if ($conn->executaSql($sql)) {
                $ok = true;
            } else {
                $ok = false;
            }
        $conn->fechaBD();

        return $ok;
    }//Fim da function recebeProcessos

/**************************************************************************
Método para recebimento de varios processos com assinatura manual
/**************************************************************************/
    public function recebeProcessosManualmente($codProcesso, $codUsuario, $codOrgao)
    {
        $codOrgaoAux       = $codOrgao;
        $sql               = "";
        $ok                = 0;

        /*  { Legado }
         *  Devido a inclusão de multi-requerentes, é necessário unificar o array
         *  para que não tenha duplicidade no código do processo.
         *  Quando o módulo for refeito, terá uma table-tree para organizar
         *  os multi-requerentes na tela de listagem, sem precisar listar mais
         *  de uma vez o mesmo processo devido ao multi-requerentes.
         */
        $codProcesso = array_unique($codProcesso);

        foreach ($codProcesso as $valor) {
            $vet = explode("-",$valor);
            $codP = $vet[0];
            $anoEx = $vet[1];
            $codAnd = $vet[2];
        }

        $sql = "
            SELECT  cod_processo_pai
                 ,  cod_processo_filho
                 ,  exercicio_pai
                 ,  exercicio_filho
              FROM  sw_processo_apensado
             WHERE  cod_processo_pai = ".$codP."
               AND  timestamp_desapensamento is null;";

        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->vaiPrimeiro();
        $registros = $conn->numeroDeLinhas;

        //Rotina para inserir os filhos(apensados)
        if ($registros > 0) {
            for ($i = 1; $i <= $registros; $i++) {
                $codProcesso_pai   = $conn->pegaCampo("cod_processo_pai");
                $codProcesso_filho = $conn->pegaCampo("cod_processo_filho");
                $exercicio_pai     = $conn->pegaCampo("exercicio_pai");
                $exercicio_filho   = $conn->pegaCampo("exercicio_filho");

                $codAndamento = pegaID("cod_andamento","sw_andamento","Where cod_processo = '$codProcesso_filho' And ano_exercicio = '".$exercicio_filho."'");
                $codAndamento = $codAndamento - 1;

                $iExercicio        = $exercicio_filho;
                $iNumRecibo        = pegaID('cod_recibo',"sw_recibo_impresso", "Where ano_exercicio = '".$iExercicio."' ");

                # Atualiza a situação do processo para 'Em andamento, a receber'
                $sql .= " UPDATE  sw_processo
                            SET  cod_situacao  = 2
                          WHERE  cod_processo  = '".$codProcesso_filho."'
                            AND  ano_exercicio = '".$exercicio_filho."'; \n";

                $sql .= " INSERT INTO sw_andamento (
                            cod_andamento, cod_processo, ano_exercicio, cod_orgao, cod_usuario, timestamp, cod_situacao )
                            VALUES
                            (".++$codAndamento.", $codProcesso_filho , '".$exercicio_filho."', $codOrgaoAux, $codUsuario, NOW(), 2); \n";

                # Insere o processo na tabela de recebimento
                $sql .= "INSERT INTO sw_recebimento (
                        cod_andamento, cod_processo, ano_exercicio
                        ) Values(
                        '".$codAndamento."','".$codProcesso_filho."','".$exercicio_filho."'); \n";

                # Insere um item de recibo manual
                $sql .= "INSERT INTO sw_recibo_impresso (
                        cod_andamento, cod_processo, ano_exercicio, cod_recibo
                        ) Values(
                        '".$codAndamento."','".$codProcesso_filho."','".$exercicio_filho."','".$iNumRecibo."'); ";

                $sql .= "INSERT INTO sw_despacho (
                        cod_andamento, cod_processo, ano_exercicio, cod_usuario, descricao, timestamp)
                        VALUES
                        ($codAndamento, $codProcesso_filho, '".$exercicio_filho."', 0, 'Emissão Manual de Recibo de Processo', NOW()); \n";

                $usuario = new usuarioLegado;
                if (Sessao::read('numCgm')) {
                    if ($dadosUsuario =  $usuario->pegaDadosUsuario(Sessao::read('numCgm'))) {
                        if (is_array($dadosUsuario)) {
                        //Grava como variável o nome da chave do vetor com o seu respectivo valor
                            foreach ($dadosUsuario as $campo=>$valor) {
                                $$campo = trim($valor);
                            }
                        }
                    }
                }

                $sql .= "INSERT INTO sw_andamento (
                         cod_andamento, cod_processo, ano_exercicio, cod_orgao, cod_usuario, timestamp, cod_situacao )
                         VALUES
                         (".++$codAndamento.", $codProcesso_filho, '".$exercicio_filho."', ".Sessao::read('codOrgao').", $codUsuario, NOW(), 2); \n";

                $conn->vaiProximo();
            }
        }

        foreach ($codProcesso as $valor) {
            $vet = explode("-",$valor);
            $codP = $vet[0];
            $anoEx = $vet[1];
            $codAnd = $vet[2];

            $iExercicio = $anoEx;
            $iNumRecibo = pegaID('cod_recibo',"sw_recibo_impresso", "Where ano_exercicio = '".$iExercicio."' ");

           //Atualiza a situação do processo para 'Em andamento, a receber'
            $sql .= "UPDATE  sw_processo
                        SET  cod_situacao = 2
                      WHERE  cod_processo = '".$codP."'
                        AND  ano_exercicio = '".$anoEx."'; \n";

            $sql .="INSERT INTO sw_andamento (
                    cod_andamento, cod_processo, ano_exercicio, cod_orgao, cod_usuario, timestamp, cod_situacao )
                    VALUES
                    (".++$codAnd.", $codP , '".$anoEx."', $codOrgaoAux, $codUsuario, NOW(), 2); \n";

            //Insere o processo na tabela de recebimento
            $sql .= "INSERT INTO sw_recebimento (
                    cod_andamento, cod_processo, ano_exercicio
                    ) Values(
                    '".$codAnd."','".$codP."','".$anoEx."'); \n";
                    //Insere um item de recibo manual

            $sql .= "INSERT INTO sw_recibo_impresso (
                    cod_andamento, cod_processo, ano_exercicio, cod_recibo
                    ) Values(
                    '".$codAnd."','".$codP."','".$anoEx."','".$iNumRecibo."'); ";

            $sql .= "INSERT INTO sw_despacho (
                    cod_andamento, cod_processo, ano_exercicio, cod_usuario, descricao, timestamp)
                    VALUES
                    ($codAnd, $codP, '".$anoEx."', 0, 'Emissão Manual de Recibo de Processo', NOW()); \n";

            $usuario = new usuarioLegado;

            if (Sessao::read('numCgm')) {
                if ($dadosUsuario =  $usuario->pegaDadosUsuario(Sessao::read('numCgm'))) {
                    if (is_array($dadosUsuario)) {
                    //Grava como variável o nome da chave do vetor com o seu respectivo valor
                        foreach ($dadosUsuario as $campo=>$valor) {
                            $$campo = trim($valor);
                        }
                    }
                }
            }

        $sql .= "INSERT INTO sw_andamento
                    (cod_andamento, cod_processo, ano_exercicio, cod_orgao, cod_usuario, timestamp, cod_situacao)
            VALUES
            (".++$codAnd.", $codP, '".$anoEx."', ".Sessao::read('codOrgao').", $codUsuario, NOW(), 2); \n";
        }

        # Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado ;
        $conn->abreBD();
        if($conn->executaSql($sql))
            $ok = $iNumRecibo;

        $conn->fechaBD();

        return $ok;
    }//Fim da function recebeProcessosManualmente

/**************************************************************************
Método para atribuir um processo a um setor
/**************************************************************************/
    public function encaminhaProcesso($codProcesso, $anoExercicio, $orgao, $codUsuario)
    {
    $sql = "
        SELECT
                cod_processo_pai
              , cod_processo_filho
              , exercicio_pai
              , exercicio_filho
          FROM  sw_processo_apensado
         WHERE  cod_processo_pai = ".$codProcesso."
           AND  timestamp_desapensamento is null;";

        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->vaiPrimeiro();
        $registros = $conn->numeroDeLinhas;

    //echo $sql;
    # Rotina para inserir os filhos(apensados)
    if ($registros > 0) {
        for ($i = 1; $i <= $registros; $i++) {
           $codProcesso_pai   = $conn->pegaCampo("cod_processo_pai");
           $codProcesso_filho = $conn->pegaCampo("cod_processo_filho");
           $exercicio_pai     = $conn->pegaCampo("exercicio_pai");
           $exercicio_filho   = $conn->pegaCampo("exercicio_filho");
            //Insere um novo andamento para o processo que deve ser o setor para o qual o processo está sendo encaminhado
            $codAndamento = pegaID("cod_andamento","sw_andamento","Where cod_processo = '$codProcesso_filho' And ano_exercicio = '".$exercicio_filho."'");
            $sql .= "
                INSERT INTO sw_andamento (
                    cod_andamento, cod_processo, ano_exercicio, cod_orgao, cod_usuario, cod_situacao
                    ) Values(
                    '".$codAndamento."', '".$codProcesso_filho."', '".$exercicio_filho."', '".$orgao."', '".$codUsuario."', 4
                    ); ";

            # MANTÉM A SITUAÇÃO DO PROCESSO COMO APENSADO
            $sql .= "UPDATE  sw_processo
                        SET  cod_situacao  = '4'
                      WHERE  cod_processo  = '".$codProcesso_filho."'
                        AND  ano_exercicio = '".$anoExercicio."'; ";
            $conn->vaiProximo();
        }
    }
    # Insere um novo andamento para o processo que deve ser o setor para o qual o processo está sendo encaminhado
    $codAndamento = pegaID("cod_andamento","sw_andamento","Where cod_processo = '$codProcesso' And ano_exercicio = '".$anoExercicio."'");

    $sql .= "INSERT INTO sw_andamento (
                cod_andamento, cod_processo, ano_exercicio, cod_orgao, cod_usuario, cod_situacao
            ) Values(
            '".$codAndamento."', '".$codProcesso."', '".$anoExercicio."', '".$orgao."', '".$codUsuario."', 2
            ); ";

    # Altera a situação do processo para 'Em andamento, a receber'
    $sql .= "
            UPDATE  sw_processo
               SET  cod_situacao  = '2'
             WHERE  cod_processo  = '".$codProcesso."'
               AND  ano_exercicio = '".$anoExercicio."'; ";

    # Chama a classe do banco de dados e executa a query
    $conn = new dataBaseLegado;
    $conn->abreBD();
    if ($conn->executaSql($sql)) {
        $ok = true;
    } else {
        $ok = false;
    }
    $conn->fechaBD();

    return $ok;
}//Fim da function recebeProcessos

/**************************************************************************
 Verifica se o tipo de processo selecionado possui andamento padrão
/**************************************************************************/
    public function verificaAndamentoPadrao($codClassif,$codAssunto)
    {
        $sql = "
            SELECT  num_passagens
              FROM  sw_andamento_padrao
             WHERE  cod_classificacao = '".$codClassif."'
               AND  cod_assunto = '".$codAssunto."' ";
        //echo $sql."<br>";
        //Chama a classe do banco de dados e executa a query
        $conn = new dataBaseLegado ;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();

        if ($conn->numeroDeLinhas > 0) {
            $matriz                   = $this->matrizAndamentoPadrao($codClassif,$codAssunto);
            # $this->setorPadrao        = $matriz[0]["codSetor"];
            # $this->departamentoPadrao = $matriz[0]["codDpto"];
            # $this->unidadePadrao      = $matriz[0]["codUnidade"];
            $this->orgaoPadrao        = $matriz[0]["codOrgao"];
            $ok = true; //O processo possui andamento padrão
        } else {
            $ok = false; //O processo não possui andamento padrão
        }
        $conn->limpaSelecao();

        return $ok;
    }//Fim function verificaAndamentoPadrao

    /**************************************************************************
     Entra com o código de classificação e assunnto de processo e retorna
     uma matriz contendo os setores em ordem de passagem
     (OBS: O índice da matriz indica a ordem)
    /**************************************************************************/
    public function matrizAndamentoPadrao($codClassif,$codAssunto)
    {
        $sql = "
              SELECT
                      sw_andamento_padrao.cod_orgao,
                      sw_andamento_padrao.num_passagens

                FROM  sw_andamento_padrao

          INNER JOIN  organograma.orgao_descricao
                  ON  orgao_descricao.cod_orgao = sw_andamento_padrao.cod_orgao

               WHERE  sw_andamento_padrao.cod_classificacao  = '".$codClassif."'
                 AND  sw_andamento_padrao.cod_assunto        = '".$codAssunto."'

            ORDER BY  sw_andamento_padrao.ordem";

        $conn = new dataBaseLegado ;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
        while (!$conn->eof()) {
            $matriz[] = array
                        (
                            numPassagens=>$conn->pegaCampo("num_passagens"),
                            codOrgao=>$conn->pegaCampo("cod_orgao"),
                            chaveSetor=>$conn->pegaCampo("cod_orgao"),
                        );
            $conn->vaiProximo();
        }
        $conn->limpaSelecao();

    return $matriz;

    }
    # Fim da function matrizAndamentoPadrao

/**************************************************************************
 Entra com o setor em que o processo está atualmente e diz qual o próximo
 setor ao qual o processo deve ser enviado
/**************************************************************************/

    public function verficaProximoAndamento($codProcesso, $anoExercicio, $codClassif, $codAssunto, $codOrgao)
    {
        # Pega o número de vezes que o processo já passou pelo setor em que se encontra atualmente
        $sql = "
            SELECT  count(*) as num
              FROM  sw_andamento
             WHERE  cod_processo = '".$codProcesso."'
               AND  ano_exercicio = '".$anoExercicio."'
               AND  cod_orgao = '".$codOrgao."'
               AND  cod_andamento <> 0";

        $conn = new dataBaseLegado ;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->vaiPrimeiro();

        if (!$conn->eof()) {
            $numPassagensEfetuadas = $conn->pegaCampo("num");
        } else {
            $numPassagensEfetuadas = 0;
        }

        $conn->limpaSelecao();
        $conn->fechaBD();

        //Monta a chave do setor onde o processo esá atualmente
        $chaveSetorAtual = $codOrgao;

        //Pega a matriz que contém o andamento padão
        $andPadrao = $this->matrizAndamentoPadrao($codClassif,$codAssunto);
        $proxAndamentoPadrao = 0;

        for ($inCount=0; $inCount<= count($andPadrao); $inCount++) {
            if ($chaveSetorAtual == $andPadrao[$inCount]['chaveSetor']) {
                if ($numPassagensEfetuadas == $andPadrao[$inCount]['numPassagens']) {
                    $proxAndamentoPadrao = $inCount+1;
                }
            }
        }

        return $proxAndamentoPadrao;
    }//Fim da function verficaProximoAndamento

/**************************************************************************
Método para capturar as variáveis para inclusão de despacho
/**************************************************************************/
    public function setaValorDespacho($codAndamento,$codProcesso,$anoExercicio,$codUsuario,$descricao)
    {
        $this->codAndamento = $codAndamento;
        $this->codProcesso  = $codProcesso;
        $this->anoExercicio = $anoExercicio;
        $this->codUsuario   = $codUsuario;
        $this->descricao    = $descricao;

    }//Fim da function setaValorDespacho

/**************************************************************************
Método para incluir um despacho a um processo
/**************************************************************************/
    public function insertDespacho()
    {
        $select = 	"SELECT
                        cod_andamento
                    FROM
                        sw_despacho
                    WHERE
                        ano_exercicio = '".$this->anoExercicio."' AND
                        cod_andamento = '".$this->codAndamento."' AND
                        cod_processo  = '".$this->codProcesso."'  AND
                        cod_usuario   = '".$this->codUsuario."'";
        //echo $select."<br>";
        $dbSelect = new databaseLegado;
        $dbSelect->abreBd();
        $dbSelect->abreSelecao($select);
        if ($dbSelect->numeroDeLinhas > 0) {
            $delete = 	"DELETE FROM
                            sw_despacho
                        WHERE
                            ano_exercicio = '".$this->anoExercicio."' AND
                            cod_andamento = '".$this->codAndamento."' AND
                            cod_processo  = '".$this->codProcesso."'  AND
                            cod_usuario   = '".$this->codUsuario."'";
            //echo $delete."<br>";
            $dbDelete = new databaseLegado;
            $dbDelete->abreBd();
            if ($dbDelete->executaSql($delete)) {
                $dbConfig = new dataBaseLegado ;
                $dbConfig->abreBd();
                $insert = 	"INSERT INTO
                                sw_despacho
                            (
                                cod_andamento,
                                cod_processo,
                                ano_exercicio,
                                cod_usuario,
                                descricao
                            )
                            VALUES
                            (
                                '".$this->codAndamento."',
                                '".$this->codProcesso."',
                                '".$this->anoExercicio."',
                                '".$this->codUsuario."',
                                '".$this->descricao."'
                            )";
                //print $insert."<br>------<br>";
                if ($dbConfig->executaSql($insert)) {
                    $dbConfig->fechaBd();

                    return true;
                } else {
                    $dbConfig->fechaBd();

                    return false;
                }
            }
        } else {
            $dbConfig = new dataBaseLegado ;
            $dbConfig->abreBd();
            $insert = 	"INSERT INTO
                            sw_despacho
                        (
                            cod_andamento,
                            cod_processo,
                            ano_exercicio,
                            cod_usuario,
                            descricao
                        )
                        VALUES
                        (
                            '".$this->codAndamento."',
                            '".$this->codProcesso."',
                            '".$this->anoExercicio."',
                            '".$this->codUsuario."',
                            '".$this->descricao."'
                        )";
            //print $insert."<br>------<br>";
            if ($dbConfig->executaSql($insert)) {
                $dbConfig->fechaBd();

                return true;
            } else {
                $dbConfig->fechaBd();

                return false;
            }
        }

    }//Fim da function insertDespacho

/**************************************************************************
Método para  editar um despacho a um processo
/**************************************************************************/
    public function updateDespacho()
    {
            $update = 	"DELETE FROM
                            sw_despacho
                        WHERE
                            ano_exercicio = '".$this->anoExercicio."' AND
                            cod_andamento = ".$this->codAndamento." AND
                            cod_processo  = ".$this->codProcesso."  AND
                            cod_usuario   = ".$this->codUsuario;
            print $update."<br>";
            $dbConfig = new dataBaseLegado ;
            $dbConfig->abreBd();
            if ($dbConfig->executaSql($update)) {
                $dbConfig->fechaBd();

                return true;
            } else {
                $dbConfig->fechaBd();

                return false;
            }
    }//Fim da function updateDespacho

/**************************************************************************
Método para  editar um processo
/**************************************************************************/
    public function updateDocumento($codDocumentos,$codProcesso,$exercicio)
    {
        if (is_array($codDocumentos)) {
            while (list($key, $val) = each($codDocumentos)) {
                $codDocumentoProcesso = pegaId("cod_copia", "sw_copia_digital", " where exercicio = '".$exercicio."' ");
                $exp = explode("=", Sessao::getId());
                $session = $exp[1];
                $diretorio = CAM_PROTOCOLO."tmp/".$session;
                //valida o diretorio e atribui o valor dos codDocumentos para cria as subpastas
                if (file_exists($diretorio)) {
                    $listaDocs = openDir($diretorio);
                    while ($dirDoc = readdir($listaDocs)) {
                        if ($dirDoc == $val) {
                            $arqDir = $diretorio."/".$val;
                            $listaArq = opendir($arqDir);
                            while ($arqDoc = readdir($listaArq)) {
                                if ($arqDoc == '.' || $arqDoc == '..') {
                                    continue;
                                }
                                $extensao = explode(".", $arqDoc);
                                $oldFile = explode("§", $fileDoc);

                                if ($extensao[1] == "jpg") {
                                    $tipoAn = "t";
                                } else {
                                    $tipoAn = "f";
                                }
                                //atribui valor do caminho da pasta de anexo dos arquivos
                                $dirAnexo = CAM_PROTOCOLO."anexos/".$codDocumentoProcesso."_".$val."_".$codProcesso."_".$exercicio.".".$extensao[1];
                                $nomearquivo = $codDocumentoProcesso."_".$val."_".$codProcesso."_".$exercicio.".".$extensao[1];
                                //atribui caminho para copiar do tmp para pasta de anexo
                                $fileDoc = $arqDir."/".$arqDoc;
                                copy($fileDoc, $dirAnexo);
                                //Busca processos e insere se não existir
                                $select = 	"SELECT
                                        cod_documento
                                    FROM
                                        sw_documento_processo
                                    WHERE
                                        cod_documento = ".$val."         AND
                                        cod_processo  = ".$codProcesso." AND
                                        exercicio     = '".$exercicio."' ";
                                $dbSelect = new databaseLegado;
                                $dbSelect->abreBd();
                                $dbSelect->abreSelecao($select);
                                if ($dbSelect->numeroDeLinhas == 0) {
                                    $insert = 	"INSERT INTO
                                                sw_documento_processo
                                                (
                                                    cod_documento,
                                                    cod_processo,
                                                    exercicio
                                                )
                                                VALUES
                                                (
                                                    ".$val.",
                                                    ".$codProcesso.",
                                                    '".$exercicio."'
                                                )";
                                    //echo $insert."<br>";
                                    $dbInsert = new databaseLegado;
                                    $dbInsert->abreBd();
                                    $dbInsert->executaSql($insert);
                                    $dbInsert->fechaBd();
                                }
                                $dbSelect->limpaSelecao();
                                $dbSelect->fechaBd();
                                //Insere Copia digital no banco
                                $insert = 	"INSERT INTO
                                            sw_copia_digital
                                        (
                                            cod_copia,
                                            cod_documento,
                                            cod_processo,
                                            exercicio,
                                            imagem,
                                            anexo
                                        )
                                    VALUES
                                        (
                                            ".$codDocumentoProcesso.",
                                            ".$val.",
                                            ".$codProcesso.",
                                            '".$exercicio."',
                                            '".$tipoAn."',
                                            '".$nomearquivo."'
                                        )";
                                //echo $insert."<br>";
                                $dbConfig = new databaseLegado;
                                $dbConfig->abreBd();
                                if ($dbConfig->executaSql($insert)) {
                                    echo "ok";
                                    $codDocumentoProcesso = $codDocumentoProcesso + 1;
                                }
                                //Deleta o arquivo inserido da pasta tmp
                                unlink($fileDoc);
                            }//fim while arqDoc
                        }//fim if dir val
                    }//fim while dirDoc

                } else {//else file_exist
                    $select = 	"SELECT
                                cod_documento
                            FROM
                                sw_documento_processo
                            WHERE
                                cod_documento = ".$val."         AND
                                cod_processo  = ".$codProcesso." AND
                                exercicio     = '".$exercicio."' ";
                    $dbSelect = new dataBaseLegado;
                    $dbSelect->abreBd();
                    $dbSelect->abreSelecao($select);
                    if ($dbSelect->numeroDeLinhas == 0) {
                        $insert = 	"INSERT INTO
                                        sw_documento_processo
                                    (
                                        cod_documento,
                                        cod_processo,
                                        exercicio
                                    )
                                    VALUES
                                    (
                                        ".$val.",
                                        ".$codProcesso.",
                                        '".$exercicio."'
                                    )";

                        $dbInsert = new databaseLegado;
                        $dbInsert->abreBd();
                        $dbInsert->executaSql($insert);
                        $dbInsert->fechaBd();
                    }
                    $dbSelect->limpaSelecao();
                    $dbSelect->fechaBd();
                }
            }//fim while dos documentos

            //Deletando arquivos e subpastas e o diretorio temporario
            if (file_exists($diretorio)) {
                foreach (scandir($diretorio) as $pasta) {
                    if ($pasta == "." || $pasta == "..") {
                        continue;
                    } else {
                        $subPasta = $diretorio."/".$pasta;
                        foreach (scandir($subPasta) as $arquivo) {
                            if ($arquivo == "." || $arquivo == "..") {
                                continue;
                            } else {
                                unlink($subPasta."/".$arquivo);
                            }
                        }
                    }
                    rmdir($diretorio."/".$pasta);
                }
                rmdir($diretorio);
            }
        } else {//fim if is_array(cod_documentos)

            return true;
        }
    }

/**************************************************************************
Método para recebimento de varios processos com assinatura digital
/**************************************************************************/
function recebeProcessosLote($arCodProcesso,$codUsuario)
{
    $sql = "";
    foreach ($arCodProcesso as $codProcesso) {
        $vet = explode("-",$codProcesso);
        $codP = $vet[0];
        $anoEx = $vet[1];
        $codAnd = $vet[2];

        $sqlConsulta = "select cod_processo_pai, cod_processo_filho, exercicio_pai, exercicio_filho
                from sw_processo_apensado where
                cod_processo_pai = ".$codP." and exercicio_pai = '".$anoEx."' and timestamp_desapensamento is null;";
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sqlConsulta);
        $conn->vaiPrimeiro();
        $registros = $conn->numeroDeLinhas;
        //Rotina para inserir os filhos(apensados)
        if ($registros > 0) {
            for ($i = 1; $i <= $registros; $i++) {
                $codProcesso_pai   = $conn->pegaCampo("cod_processo_pai");
                $codProcesso_filho = $conn->pegaCampo("cod_processo_filho");
                $exercicio_pai     = $conn->pegaCampo("exercicio_pai");
                $exercicio_filho   = $conn->pegaCampo("exercicio_filho");
                $codAndamento = pegaID("cod_andamento","sw_andamento","Where cod_processo = '$codProcesso_filho' And ano_exercicio = '".$exercicio_filho."'");
                $codAndamento = $codAndamento - 1;
                //Insere o processo na tabela de recebimento
                $sql .= "Insert Into sw_recebimento (
                         cod_andamento, cod_processo, ano_exercicio
                         ) Values(
                         '".$codAndamento."','".$codProcesso_filho."','".$exercicio_filho."'); ";
                //Insere uma assinatura digital com o código do logado
                $sql .= "Insert Into sw_assinatura_digital (
                        cod_andamento, cod_processo, ano_exercicio, cod_usuario
                        ) Values(
                        '".$codAndamento."','".$codProcesso_filho."','".$exercicio_filho."','".$codUsuario."'); ";
                //Insere um despacho nos filhos(apensados)
                $descricao= "Trâmite em Apenso ao Processo N. ".mascaraProcesso($codProcesso_filho, $exercicio_filho);
                $this->setaValorDespacho($codAndamento,$codProcesso_filho,$anoExercicio,0,$descricao);
                $select =   "SELECT cod_andamento FROM sw_despacho WHERE
                                ano_exercicio = '".$exercicio_filho."' AND
                                cod_andamento = ".$this->codAndamento." AND
                                cod_processo  = ".$this->codProcesso."  AND
                                cod_usuario   = ".$this->codUsuario;
                $dbSelect = new databaseLegado;
                $dbSelect->abreBd();
                $dbSelect->abreSelecao($select);
                if ($dbSelect->numeroDeLinhas > 0) {
                    $sql .= "DELETE FROM sw_despacho WHERE
                                    ano_exercicio = '".$exercicio_filho."' AND
                                    cod_andamento = ".$this->codAndamento." AND
                                    cod_processo  = ".$this->codProcesso."  AND
                                    cod_usuario   = ".$this->codUsuario.";";
                    $sql .= "INSERT INTO sw_despacho(
                                cod_andamento,cod_processo,ano_exercicio,
                                cod_usuario,descricao
                             )VALUES(
                                ".$this->codAndamento.",".$this->codProcesso.",'".$exercicio_filho."',
                                ".$this->codUsuario.",'".$this->descricao."'
                             );";
                } else {
                    $sql .= "INSERT INTO sw_despacho(
                                cod_andamento,cod_processo,ano_exercicio,
                                cod_usuario,descricao
                             )VALUES(
                                ".$this->codAndamento.",".$this->codProcesso.",'".$exercicio_filho."',
                                ".$this->codUsuario.",'".$this->descricao."'
                             );";
                }
                $conn->vaiProximo();
            }
        }
        //Insere o processo na tabela de recebimento
        $sql .= "Insert Into sw_recebimento (
                cod_andamento, cod_processo, ano_exercicio
                ) Values(
                '".$codAnd."','".$codP."','".$anoEx."'); ";
        //Insere uma assinatura digital com o código do logado
        $sql .= "Insert Into sw_assinatura_digital (
                cod_andamento, cod_processo, ano_exercicio, cod_usuario
                ) Values(
                '".$codAnd."','".$codP."','".$anoEx."','".$codUsuario."'); ";
        //Atualiza a situação do processo para 'Em andamento, recebido'
        $sql .= "Update sw_processo
                Set cod_situacao = '3'
                Where cod_processo = '".$codP."'
                And ano_exercicio = '".$anoEx."'; ";
    }
    //Chama a classe do banco de dados e executa a query
    $conn = new dataBaseLegado ;
    $conn->abreBD();
    if ($conn->executaSql($sql)) {
        $ok = true;
    } else {
        $ok = false;
    }
    $conn->fechaBD();

    return $ok;
}//Fim da function recebeProcessosLote

/**************************************************************************
Método para atribuir um processo a um setor em lote
/**************************************************************************/
function encaminhaProcessoLote($arProcessos, $orgao, $codUsuario)
{
    foreach ($arProcessos as $stChaveProcesso) {
        $arProcesso   = explode( "-", $stChaveProcesso );
        $codProcesso  = $arProcesso[0];
        $anoExercicio = $arProcesso[1];
        $stSql  = " SELECT                                     \n";
        $stSql .= "      cod_processo_pai                      \n";
        $stSql .= "     ,cod_processo_filho                    \n";
        $stSql .= "     ,exercicio_pai                         \n";
        $stSql .= "     ,exercicio_filho                       \n";
        $stSql .= " FROM                                       \n";
        $stSql .= "    sw_processo_apensado                    \n";
        $stSql .= " WHERE                                      \n";
        $stSql .= "    cod_processo_pai = ".$codProcesso."     \n";
        $stSql .= "    AND exercicio_pai = '".$anoExercicio."' \n";
        $stSql .= "    AND timestamp_desapensamento IS NULL;   \n";
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($stSql);
        $conn->vaiPrimeiro();
        $registros = $conn->numeroDeLinhas;        
        if ($registros > 0) {
            for ($i = 1; $i <= $registros; $i++) {
                $codProcesso_pai   = $conn->pegaCampo("cod_processo_pai");
                $codProcesso_filho = $conn->pegaCampo("cod_processo_filho");
                $exercicio_pai     = $conn->pegaCampo("exercicio_pai");
                $exercicio_filho   = $conn->pegaCampo("exercicio_filho");
                //Insere um novo andamento para o proc. que deve ser o setor para o qual o processo está sendo encaminhado
                $stFiltro = "Where cod_processo = '$codProcesso_filho' And ano_exercicio = '".$exercicio_filho."'";

                $codAndamento = pegaID("cod_andamento","sw_andamento", $stFiltro );
                $codSituacao = pegaID("cod_situacao","sw_andamento", $stFiltro );

                $sql .= " INSERT INTO sw_andamento (                                                 \n";
                $sql .= "     cod_andamento, cod_processo, ano_exercicio,                            \n";
                $sql .= "     cod_orgao, cod_usuario, cod_situacao                                                \n";
                $sql .= " ) VALUES(                                                                  \n";
                $sql .= "     '".$codAndamento."', '".$codProcesso_filho."', '".$exercicio_filho."', \n";
                $sql .= "     '".$orgao."', '".$codUsuario."', '".$codSituacao."'                                \n";
                $sql .= " );                                                                         \n";
                //MANTÉM A SITUAÇÃO DO PROCESSO COMO APENSADO
                $sql .= " UPDATE  sw_processo                           \n";
                $sql .= "    SET  cod_situacao = '4'                    \n";
                $sql .= "  WHERE                                        \n";
                $sql .= "     cod_processo = '".$codProcesso_filho."'   \n";
                $sql .= "     AND ano_exercicio = '".$anoExercicio."';  \n";
                $conn->vaiProximo();
            }
        }
        //Insere um novo andamento para o processo que deve ser o setor para o qual o processo está sendo encaminhado
        $stFiltro = " WHERE cod_processo = '$codProcesso' AND ano_exercicio = '".$anoExercicio."'";

        $codAndamento = pegaID("cod_andamento","sw_andamento",$stFiltro);        
        $codSituacao = pegaValor("  SELECT MAX(cod_situacao) as cod_situacao 
                                    FROM sw_andamento ". $stFiltro, "cod_situacao" );

        $sql .= " INSERT INTO sw_andamento (                                        \n";
        $sql .= "     cod_andamento, cod_processo, ano_exercicio,                   \n";
        $sql .= "     cod_orgao, cod_usuario, cod_situacao                                        \n";
        $sql .= " ) VALUES(                                                         \n";
        $sql .= "     '".$codAndamento."', '".$codProcesso."', '".$anoExercicio."', \n";
        $sql .= "     '".$orgao."', '".$codUsuario."', '".$codSituacao."'                              \n";
        $sql .= " );                                                                \n";
        //Altera a situação do processo para 'Em andamento, a receber'
        $sql .= " UPDATE                                      \n";
        $sql .= "    sw_processo SET cod_situacao = '2'       \n";
        $sql .= " WHERE                                       \n";
        $sql .= "    cod_processo = '".$codProcesso."'        \n";
        $sql .= "    AND ano_exercicio = '".$anoExercicio."'; \n";
    }
    //Chama a classe do banco de dados e executa a query
    $conn = new dataBaseLegado ;
    $conn->abreBD();
        if ($conn->executaSql($sql)) {
            $ok = true;
        } else {
            $ok = false;
        }
    $conn->fechaBD();

    return $ok;
}//Fim da function encaminhaProcessosLote

}
?>
